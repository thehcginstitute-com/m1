<?php
# 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
# 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as D;
use HCG\MailChimp\Model\Api\Batches as Plugin;
final class Ebizmarts_MailChimp_Model_Api_Batches {
	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Orders
	 */
	function getApiOrders() {return dfc($this, function() {return Mage::getModel('mailchimp/api_orders');});}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Products
	 */
	function getApiProducts() {return dfc($this, function() {return Mage::getModel('mailchimp/api_products');});}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_PromoCodes
	 */
	function getApiPromoCodes() {return dfc($this, function() {return Mage::getModel('mailchimp/api_promoCodes');});}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_PromoRules
	 */
	function getApiPromoRules() {return dfc($this, function() {return Mage::getModel('mailchimp/api_promoRules');});}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Subscribers
	 */
	function getApiSubscribers() {return dfc($this, function() {return Mage::getModel('mailchimp/api_subscribers');});}

	/**
	 * @return Ebizmarts_MailChimp_Model_Synchbatches
	 */
	private function getSyncBatchesModel()
	{
		return Mage::getModel('mailchimp/synchbatches');
	}

	/**
	 * @return array
	 */
	private function getStores()
	{
		return Mage::app()->getStores();
	}

	/**
	 * @return string
	 */
	function getMagentoBaseDir()
	{
		return Mage::getBaseDir();
	}

	/**
	 * Get Results and send Ecommerce Batches.
	 */
	function handleEcommerceBatches()
	{
		$helper = hcg_mc_h();
		$stores = $this->getStores();
		$helper->handleResendDataBefore();

		foreach ($stores as $store) {
			$storeId = $store->getId();

			if ($helper->isEcomSyncDataEnabled($storeId)) {
				if ($helper->ping($storeId)) {
					$this->_getResults($storeId);
					$this->_sendEcommerceBatch($storeId);
				} else {
					$helper->logError(
						"Could not connect to MailChimp: Make sure the API Key is correct "
						. "and there is an internet connection"
					);
					return;
				}
			}
		}

		$helper->handleResendDataAfter();
		$syncedDateArray = array();

		foreach ($stores as $store) {
			$storeId = $store->getId();
			$syncedDateArray = $this->addSyncValueToArray($storeId, $syncedDateArray);
		}

		$this->handleSyncingValue($syncedDateArray);
	}

	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by Ebizmarts_MailChimp_Model_Cron::syncSubscriberBatchData()
	 */
	function handleSubscriberBatches():void	{
		$limit = (int)Mage::getStoreConfig(Cfg::GENERAL_SUBSCRIBER_AMOUNT, 0); /** @var int $limit */
		# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# https://3v4l.org/AF1Vc
		foreach ($this->getStores() as $s) {
			$sid = (int)$s->getId(); /** @var int $sid */
			$this->_getResults($sid, false);
			if (1 > $limit) {
				break;
			}
			$limit = $this->sendStoreSubscriberBatch($sid, $limit);
		}
		$this->_getResults(0, false);
		if (0 < $limit) {
			$this->sendStoreSubscriberBatch(0, $limit);
		}
	}

	/**
	 * Get results of batch operations sent to MailChimp.
	 *
	 * @param       $magentoStoreId
	 * @param bool  $isEcommerceData
	 * @throws Mage_Core_Exception
	 */
	function _getResults($magentoStoreId, $isEcommerceData = true, $status = Ebizmarts_MailChimp_Helper_Data::BATCH_PENDING)
	{
		$helper = hcg_mc_h();
		$mailchimpStoreId = $helper->getMCStoreId($magentoStoreId);
		$collection = $this->getSyncBatchesModel()->getCollection()->addFieldToFilter('status', array('eq' => $status));

		if ($isEcommerceData) {
			$collection->addFieldToFilter('store_id', array('eq' => $mailchimpStoreId));
			$enabled = $helper->isEcomSyncDataEnabled($magentoStoreId);
		} else {
			$collection->addFieldToFilter('store_id', array('eq' => $magentoStoreId));
			$enabled = $helper->isSubscriptionEnabled($magentoStoreId);
		}

		if ($enabled) {
			$helper->logBatchStatus('Get results from Mailchimp for Magento store ' . $magentoStoreId);

			foreach ($collection as $item) {
				try {
					$batchId = $item->getBatchId();
					$files = $this->getBatchResponse($batchId, $magentoStoreId);
					$this->_saveItemStatus($item, $files, $batchId, $mailchimpStoreId, $magentoStoreId);
					hcg_mc_batch_delete($batchId);
				} catch (Exception $e) {
					Mage::log("Error with a response: " . $e->getMessage());
				}
			}
		}
	}

	/**
	 * @param $item
	 * @param $files
	 * @param $batchId
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 */
	private function _saveItemStatus($item, $files, $batchId, $mailchimpStoreId, $magentoStoreId)
	{
		$helper = hcg_mc_h();

		if (!empty($files)) {
			if (isset($files['error'])) {
				$item->setStatus('error');
				$item->save();
				$helper->logBatchStatus('There was an error getting the result ');
			} else {
				$this->processEachResponseFile($files, $batchId, $mailchimpStoreId, $magentoStoreId);
				$item->setStatus('completed');
				$item->save();
			}
		}
	}

	/**
	 * Send Customers, Products, Orders, Carts to MailChimp store for given scope.
	 * Return true if MailChimp store is reset in the process.
	 *
	 * @param  $magentoStoreId
	 * @throws Mage_Core_Exception
	 */
	function _sendEcommerceBatch($magentoStoreId)
	{
		$helper = hcg_mc_h();
		$mailchimpStoreId = $helper->getMCStoreId($magentoStoreId);

		try {
			$this->deleteUnsentItems();

			if ($helper->isEcomSyncDataEnabled($magentoStoreId)) {
				$helper->resetCountersSentPerBatch();
				$batchArray = array();
				//customer operations
				$helper->logBatchStatus('Generate Customers Payload');
				/** @var Ebizmarts_MailChimp_Model_Api_Customers $apiCustomers */
				$apiCustomers = new Ebizmarts_MailChimp_Model_Api_Customers;
				$apiCustomers->setMailchimpStoreId($mailchimpStoreId);
				$apiCustomers->setMagentoStoreId($magentoStoreId);

				$customersArray = $apiCustomers->createBatchJson();
				$batchArray['operations'] = $customersArray;

				//product operations
				$helper->logBatchStatus('Generate Products Payload');

				$apiProducts = $this->getApiProducts();
				$apiProducts->setMailchimpStoreId($mailchimpStoreId);
				$apiProducts->setMagentoStoreId($magentoStoreId);

				$productsArray = $apiProducts->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $productsArray);

				if ($helper->getMCIsSyncing($mailchimpStoreId, $magentoStoreId) === 1) {
					$helper->logBatchStatus('No Carts will be synced until the store is completely synced');
				} else {
					//cart operations
					$helper->logBatchStatus('Generate Carts Payload');
					/** @var Ebizmarts_MailChimp_Model_Api_Carts $apiCarts */
					$apiCarts = new Ebizmarts_MailChimp_Model_Api_Carts;
					$apiCarts->setMailchimpStoreId($mailchimpStoreId);
					$apiCarts->setMagentoStoreId($magentoStoreId);

					$cartsArray = $apiCarts->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $cartsArray);
				}

				//order operations
				$helper->logBatchStatus('Generate Orders Payload');
				$apiOrders = $this->getApiOrders();
				$apiOrders->setMailchimpStoreId($mailchimpStoreId);
				$apiOrders->setMagentoStoreId($magentoStoreId);

				$ordersArray = $apiOrders->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $ordersArray);

				if ($helper->getPromoConfig($magentoStoreId) == self::SEND_PROMO_ENABLED) {
					//promo rule operations
					$helper->logBatchStatus('Generate Promo Rules Payload');
					$apiPromoRules = $this->getApiPromoRules();
					$apiPromoRules->setMailchimpStoreId($mailchimpStoreId);
					$apiPromoRules->setMagentoStoreId($magentoStoreId);

					$promoRulesArray = $apiPromoRules->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $promoRulesArray);

					//promo code operations
					$helper->logBatchStatus('Generate Promo Codes Payload');
					$apiPromoCodes = $this->getApiPromoCodes();
					$apiPromoCodes->setMailchimpStoreId($mailchimpStoreId);
					$apiPromoCodes->setMagentoStoreId($magentoStoreId);

					$promoCodesArray = $apiPromoCodes->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $promoCodesArray);
				}

				//deleted product operations
				$helper->logBatchStatus('Generate Deleted Products Payload');
				$deletedProductsArray = $apiProducts->createDeletedProductsBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $deletedProductsArray);
				$batchJson = null;
				$batchResponse = null;

				try {
					$this->_processBatchOperations($batchArray, $mailchimpStoreId, $magentoStoreId);
					$this->_updateSyncingFlag($mailchimpStoreId, $magentoStoreId);
				} catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
					$helper->logError($e->getMessage());
				} catch (MailChimp_Error $e) {
					$helper->logError($e->getFriendlyMessage());

					if ($batchJson && !isset($batchResponse['id'])) {
						$helper->logRequest($batchJson);
					}
				} catch (Exception $e) {
					$helper->logError($e->getMessage());
					$helper->logError("Json encode fails");
					$helper->logError($batchArray);
				}
			}
		} catch (MailChimp_Error $e) {
			$helper->logError($e->getFriendlyMessage());
		} catch (Exception $e) {
			$helper->logError($e->getMessage());
		}
	}

	/**
	 * @param $batchArray
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Ebizmarts_MailChimp_Helper_Data_ApiKeyException
	 * @throws Mage_Core_Exception
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */

	private function _processBatchOperations($batchArray, $mailchimpStoreId, $magentoStoreId)
	{
		$helper = hcg_mc_h();
		$mailchimpApi = $helper->getApi($magentoStoreId);

		if (!empty($batchArray['operations'])) {
			$batchJson = json_encode($batchArray);

			if ($batchJson === false) {
				$helper->logRequest('Json encode error ' . json_last_error_msg());
			} elseif (empty($batchJson)) {
				$helper->logRequest('An empty operation was detected');
			} else {
				$helper->logBatchStatus('Send batch operation');
				$batchResponse = $mailchimpApi->getBatchOperation()->add($batchJson);
				$helper->logRequest($batchJson, $batchResponse['id']);
				//save batch id to db
				$batch = $this->getSyncBatchesModel();
				$batch->setStoreId($mailchimpStoreId)->setBatchId($batchResponse['id'])->setStatus($batchResponse['status']);
				$batch->save();
				$this->markItemsAsSent($batchResponse['id'], $mailchimpStoreId);
				$this->_showResumeEcommerce($batchResponse['id'], $magentoStoreId);
			}
		} else {
			$helper->logBatchStatus("Nothing to sync for store $magentoStoreId");
		}
	}

	/**
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function _updateSyncingFlag(
		$mailchimpStoreId,
		$magentoStoreId
	) {
		$helper = hcg_mc_h();
		$dateHelper = $this->getDateHelper();
		$itemAmount = $helper->getTotalNewItemsSent();
		$syncingFlag = $helper->getMCIsSyncing($mailchimpStoreId, $magentoStoreId);

		if ($this->shouldFlagAsSyncing($syncingFlag, $itemAmount, $helper)) {
			//Set is syncing per scope in 1 until sync finishes.
			hcg_mc_cfg_save(Ebizmarts_MailChimp_Model_Config::GENERAL_MCISSYNCING . "_$mailchimpStoreId", 1, $magentoStoreId, 'stores');
		} else {
			if ($this->shouldFlagAsSynced($syncingFlag, $itemAmount)) {
				//Set is syncing per scope to a date because it is not sending any more items.
				hcg_mc_cfg_save(
					Ebizmarts_MailChimp_Model_Config::GENERAL_MCISSYNCING . "_$mailchimpStoreId"
					,$dateHelper->formatDate(null, 'Y-m-d H:i:s')
					,$magentoStoreId
					,'stores'
				);
			}
		}
	}

	/**
	 * @param $batchId
	 */
	private function deleteBatchItems($batchId)
	{
		$helper = hcg_mc_h();
		$resource = $helper->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = array("batch_id = '$batchId'");
		$connection->delete($tableName, $where);
	}

	private function deleteUnsentItems()
	{
		$helper = hcg_mc_h();
		$resource = $helper->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = array("batch_id IS NULL AND mailchimp_sync_modified != 1 AND mailchimp_sync_deleted != 1");
		$connection->delete($tableName, $where);
	}

	function ecommerceDeleteCallback($args)
	{
		$ecommerceData = Mage::getModel('mailchimp/ecommercesyncdata');
		$ecommerceData->setData($args['row']);
		$ecommerceData->delete();
	}

	private function markItemsAsSent($batchResponseId, $mailchimpStoreId)
	{
		$helper = hcg_mc_h();
		$dateHelper = $this->getDateHelper();

		$resource = $helper->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = array("batch_id IS NULL AND mailchimp_store_id = ?" => $mailchimpStoreId);
		$connection->update(
			$tableName,
			array(
				'batch_id' => $batchResponseId,
				'mailchimp_sync_delta' => $dateHelper->formatDate(null, 'Y-m-d H:i:s')
			),
			$where
		);
	}

	/**
	 * 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 */
	function ecommerceSentCallback($args) {
		$d = hcg_mc_syncd_new(); /** @var D $d */
		$d->setData($args['row']); // map data to customer model
		$writeAdapter = Mage::getSingleton('core/resource')->getConnection('core_write');
		$insertData = array(
			'id' => $d->getId(),
			'related_id' => $d->getRelatedId(),
			'type' => $d->getType(),
			'mailchimp_store_id' => $d->getMailchimpStoreId(),
			'mailchimp_sync_error' => $d['mailchimp_sync_error'],
			'mailchimp_sync_delta' => $d->time(),
			'mailchimp_sync_modified' => $d->getMailchimpSyncModified(),
			'mailchimp_sync_deleted' => $d->getMailchimpSyncDeleted(),
			'mailchimp_token' => $d->getMailchimpToken(),
			'batch_id' => $d->getBatchId()
		);
		$resource = Mage::getResourceModel('mailchimp/ecommercesyncdata');
		$writeAdapter->insertOnDuplicate(
			$resource->getMainTable(),
			$insertData,
			array(
				'id',
				'related_id',
				'type',
				'mailchimp_store_id',
				'mailchimp_sync_error',
				'mailchimp_sync_delta',
				'mailchimp_sync_modified',
				'mailchimp_sync_deleted',
				'mailchimp_token',
				'batch_id'
			)
		);
	}

	/**
	 * 2024-04-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()`":
	 * https://github.com/thehcginstitute-com/m1/issues/571
	 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::downloadresponseAction()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_getResults()
	 * @param $batchId
	 * @param $magentoStoreId
	 */
	function getBatchResponse($batchId, $magentoStoreId):array {
		$helper = hcg_mc_h();
		$fileHelper = $this->getMailchimpFileHelper();
		$r = []; /** @var array $r */
		try {
			$baseDir = $this->getMagentoBaseDir();
			$api = $helper->getApi($magentoStoreId);
			if ($fileHelper->isDir(hcg_mc_batches_path()) == false) {
				$fileHelper->mkDir(hcg_mc_batches_path());
			}
			if ($api) {
				// check the status of the job
				$response = $api->batchOperation->status($batchId);
				if (isset($response['status']) && $response['status'] == 'finished') {
					// get the tar.gz file with the results
					$fileUrl = urldecode($response['response_body_url']);
					$fileName = hcg_mc_batches_path($batchId) . '.tar.gz';
					$fd = fopen($fileName, 'w');
					$curlOptions = array(
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FILE => $fd,
						CURLOPT_FOLLOWLOCATION => true, // this will follow redirects
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					);
					$curlHelper = $this->getMailchimpCurlHelper();
					$curlHelper->curlExec($fileUrl, Zend_Http_Client::GET, $curlOptions);
					fclose($fd);
					$fileHelper->mkDir(hcg_mc_batches_path($batchId), 0750, true);
					$archive = new Mage_Archive();
					if ($fileHelper->fileExists($fileName)) {
						$r = $this->_unpackBatchFile($r, $batchId, $archive, $fileName, $baseDir);
					}
				}
			}
		}
		catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
			$helper->logError($e->getMessage());
			$r['error'] = $e->getMessage();
		}
		catch (MailChimp_Error $e) {
			$this->deleteBatchItems($batchId);
			$r['error'] = $e->getFriendlyMessage();
			$helper->logError($e->getFriendlyMessage());
		}
		catch (Exception $e) {
			$r['error'] = $e->getMessage();
			$helper->logError($e->getMessage());
		}
		return $r;
	}

	/**
	 * @param $files
	 * @param $batchId
	 * @param $archive Mage_Archive
	 * @param $fileName
	 * @param $baseDir
	 * @return array
	 */
	private function _unpackBatchFile($files, $batchId, $archive, $fileName, $baseDir)
	{
		$path = hcg_mc_batches_path($batchId);
		$archive->unpack($fileName, $path);
		$archive->unpack($path . DS . $batchId . '.tar', $path);
		$fileHelper = $this->getMailchimpFileHelper();
		$dirItems = new DirectoryIterator($path);

		foreach ($dirItems as $index => $dirItem) {

			if ($dirItem->isFile() && $dirItem->getExtension() == 'json'){
				$files[] = $path . DS . $dirItem->getBasename();
			}
		}
		$fileHelper->rm($path . DS . $batchId . '.tar');
		$fileHelper->rm($fileName);

		return $files;
	}

	/**
	 * @param $files
	 * @param $batchId
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 */
	private function processEachResponseFile($files, $batchId, $mailchimpStoreId, $magentoStoreId) {
		$helper = hcg_mc_h();
		$helper->resetCountersDataSentToMailchimp();
		$fileHelper = $this->getMailchimpFileHelper();
		$fileHelper->open(['path '=> hcg_mc_batches_path()]);
		foreach ($files as $file) {
			$fileContent = $fileHelper->read($file);
			$items = json_decode($fileContent, true);
			if ($items !== false) {
				foreach ($items as $item) {
					$line = explode('_', $item['operation_id']);
					$store = explode('-', $line[0]);
					$type = $line[1];
					$id = $line[3];
					if ($item['status_code'] != 200) {
						# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
						# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
						Plugin::handleErrorItem($this, $item, $batchId, $mailchimpStoreId, $id, $type, $store);
					}
					else {
						$syncDataItem = $this->getDataProduct($mailchimpStoreId, $id, $type);
						if (!$syncDataItem->getMailchimpSyncModified()) {
							$syncModified = $this->enableMergeFieldsSending($type, $syncDataItem);
							$this->saveSyncData(
								$id,
								$type,
								$mailchimpStoreId,
								null,
								'',
								$syncModified,
								null,
								null,
								1,
								true
							);
							$helper->modifyCounterDataSentToMailchimp($type);
						} else {
							$this->saveSyncData(
								$id,
								$type,
								$mailchimpStoreId,
								null,
								'',
								0,
								null,
								null,
								1,
								true
							);
						}
					}
				}
			}
			$fileHelper->rm($file);
		}
		$this->_showResumeDataSentToMailchimp($magentoStoreId);
	}

	/**
	 * @used-by HCG\MailChimp\Model\Api\Batches::handleErrorItem()
	 * @param $type
	 * @param $mailchimpStoreId
	 * @param $id
	 * @param $response
	 * @return string
	 */
	function _getError($type, $mailchimpStoreId, $id, $response)
	{
		$error = $response['title'] . " : " . $response['detail'];

		if ($type == Ebizmarts_MailChimp_Model_Config::IS_PRODUCT) {
			$dataProduct = $this->getDataProduct($mailchimpStoreId, $id, $type);
			$isProductDisabledInMagento = Ebizmarts_MailChimp_Model_Api_Products::PRODUCT_DISABLED_IN_MAGENTO;

			if ($dataProduct->getMailchimpSyncDeleted()
				|| $dataProduct['mailchimp_sync_error'] == $isProductDisabledInMagento
			) {
				$error = $isProductDisabledInMagento;
			}
		}

		return $error;
	}

	/**
	 * @used-by HCG\MailChimp\Model\Api\Batches::handleErrorItem()
	 * @param $response
	 * @return string
	 */
	function _processFileErrors($response)
	{
		$errorDetails = "";

		if (!empty($response['errors'])) {
			foreach ($response['errors'] as $error) {
				if (isset($error['field']) && isset($error['message'])) {
					$errorDetails .= $errorDetails != "" ? " / " : "";
					$errorDetails .= $error['field'] . " : " . $error['message'];
				}
			}
		}

		if ($errorDetails == "") {
			$errorDetails = $response['detail'];
		}

		return $errorDetails;
	}

	/**
	 * Handle batch for order id replacement with the increment id in MailChimp.
	 *
	 * @param $initialTime
	 * @param $magentoStoreId
	 */
	function replaceAllOrders($initialTime, $magentoStoreId)
	{
		$helper = hcg_mc_h();
		try {
			$this->_getResults($magentoStoreId);

			//handle order replacement
			$mailchimpStoreId = $helper->getMCStoreId($magentoStoreId);

			$batchArray['operations'] = Mage::getModel('mailchimp/api_orders')->replaceAllOrdersBatch(
				$initialTime,
				$mailchimpStoreId,
				$magentoStoreId
			);
			try {
				/**
				 * @var $mailchimpApi Ebizmarts_MailChimp
				 */
				$mailchimpApi = $helper->getApi($magentoStoreId);

				if (!empty($batchArray['operations'])) {
					$batchJson = json_encode($batchArray);

					if ($batchJson === false) {
						$helper->logRequest('Json encode error: ' . json_last_error_msg());
					} elseif ($batchJson == '') {
						$helper->logRequest('An empty operation was detected');
					} else {
						$batchResponse = $mailchimpApi->batchOperation->add($batchJson);
						$helper->logRequest($batchJson, $batchResponse['id']);
						//save batch id to db
						$batch = $this->getSyncBatchesModel();
						$batch->setStoreId($mailchimpStoreId)
							->setBatchId($batchResponse['id'])
							->setStatus($batchResponse['status']);
						$batch->save();
					}
				}
			} catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
				$helper->logError($e->getMessage());
			} catch (MailChimp_Error $e) {
				$helper->logError($e->getFriendlyMessage());
			} catch (Exception $e) {
				$helper->logError($e->getMessage());
				$helper->logError("Json encode fails");
				$helper->logError($batchArray);
			}
		} catch (MailChimp_Error $e) {
			$helper->logError($e->getFriendlyMessage());
		} catch (Exception $e) {
			$helper->logError($e->getMessage());
		}
	}

	/**
	 * @used-by HCG\MailChimp\Model\Api\Batches::handleErrorItem()
	 * @param       $itemId
	 * @param       $itemType
	 * @param       $mailchimpStoreId
	 * @param null  $syncDelta
	 * @param null  $syncError
	 * @param int   $syncModified
	 * @param null  $syncDeleted
	 * @param null  $token
	 * @param null  $syncedFlag
	 * @param bool  $saveOnlyIfExists
	 */
	function saveSyncData(
		$itemId,
		$itemType,
		$mailchimpStoreId,
		$syncDelta = null,
		$syncError = null,
		$syncModified = 0,
		$syncDeleted = null,
		$token = null,
		$syncedFlag = null,
		$saveOnlyIfExists = false
	) {
		$helper = hcg_mc_h();

		if ($itemType == Ebizmarts_MailChimp_Model_Config::IS_SUBSCRIBER) {
			$helper->updateSubscriberSyndData($itemId, $syncDelta, $syncError, 0, null);
		} else {
			hcg_mc_syncd_new()->saveEcommerceSyncData(
				$itemId,
				$itemType,
				$mailchimpStoreId,
				$syncDelta,
				$syncError,
				$syncModified,
				$syncDeleted,
				$token,
				$syncedFlag,
				$saveOnlyIfExists,
				null,
				false
			);
		}
	}

	/**
	 * @param $storeId
	 * @param $syncedDateArray
	 * @return mixed
	 */
	private function addSyncValueToArray($storeId, $syncedDateArray)
	{
		$helper = hcg_mc_h();
		$ecomEnabled = $helper->isEcomSyncDataEnabled($storeId);

		if ($ecomEnabled) {
			$mailchimpStoreId = $helper->getMCStoreId($storeId);
			$syncedDate = $helper->getMCIsSyncing($mailchimpStoreId, $storeId);

			// Check if $syncedDate is in date format to support previous versions.
			if (isset($syncedDateArray[$mailchimpStoreId]) && $syncedDateArray[$mailchimpStoreId]) {
				if ($helper->validateDate($syncedDate)) {
					if ($syncedDate > $syncedDateArray[$mailchimpStoreId]) {
						$syncedDateArray[$mailchimpStoreId] = array($storeId => $syncedDate);
					}
				} elseif ((int)$syncedDate === 1) {
					$syncedDateArray[$mailchimpStoreId] = array($storeId => false);
				}
			} else {
				if ($helper->validateDate($syncedDate)) {
					$syncedDateArray[$mailchimpStoreId] = array($storeId => $syncedDate);
				} else {
					if ((int)$syncedDate === 1 || $syncedDate === null) {
						$syncedDateArray[$mailchimpStoreId] = array($storeId => false);
					} elseif (!isset($syncedDateArray[$mailchimpStoreId])) {
						$syncedDateArray[$mailchimpStoreId] = array($storeId => true);
					}
				}
			}
		}

		return $syncedDateArray;
	}

	/**
	 * @param $syncedDateArray
	 * @throws Mage_Core_Exception
	 */
	function handleSyncingValue($syncedDateArray)
	{
		$helper = hcg_mc_h();
		foreach ($syncedDateArray as $mailchimpStoreId => $val) {
			$magentoStoreId = key($val);
			$date = $val[$magentoStoreId];
			$ecomEnabled = $helper->isEcomSyncDataEnabled($magentoStoreId);
			if ($ecomEnabled && $date) {
				try {
					$api = $helper->getApi($magentoStoreId);
					$isSyncingDate = $helper->getDateSyncFinishByMailChimpStoreId($mailchimpStoreId);
					if (!$isSyncingDate && $mailchimpStoreId) {
						$this->getApiStores()->editIsSyncing($api, false, $mailchimpStoreId);
						hcg_mc_cfg_save(Ebizmarts_MailChimp_Model_Config::ECOMMERCE_SYNC_DATE . "_$mailchimpStoreId", $date);
					}
				} catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
					$helper->logError($e->getMessage());
				} catch (MailChimp_Error $e) {
					$helper->logError($e->getFriendlyMessage());
				} catch (Exception $e) {
					$helper->logError($e->getMessage());
				}
			}
		}
	}

	/**
	 * @used-by HCG\MailChimp\Model\Api\Batches::handleErrorItem()
	 * @param $mailchimpStoreId
	 * @param $id
	 * @param $type
	 */
	function setItemAsModified($mailchimpStoreId, $id, $type)
	{
		$isMarkedAsDeleted = null;

		if ($type == Ebizmarts_MailChimp_Model_Config::IS_PRODUCT) {
			$dataProduct = $this->getDataProduct($mailchimpStoreId, $id, $type);
			$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();
			$isProductDisabledInMagento = Ebizmarts_MailChimp_Model_Api_Products::PRODUCT_DISABLED_IN_MAGENTO;

			if (!$isMarkedAsDeleted || $dataProduct['mailchimp_sync_error']!= $isProductDisabledInMagento) {
				$this->saveSyncData(
					$id,
					$type,
					$mailchimpStoreId,
					null,
					null,
					1,
					0,
					null,
					1,
					true
				);
			} else {
				$this->saveSyncData(
					$id,
					$type,
					$mailchimpStoreId,
					null,
					$isProductDisabledInMagento,
					0,
					1,
					null,
					0,
					true
				);
			}
		} else {
			$this->saveSyncData(
				$id,
				$type,
				$mailchimpStoreId,
				null,
				null,
				1,
				0,
				null,
				1,
				true
			);
		}
	}

	/**
	 * @param $syncingFlag
	 * @param $itemAmount
	 * @param $helper
	 * @return bool
	 */
	private function shouldFlagAsSyncing($syncingFlag, $itemAmount, $helper)
	{
		return $syncingFlag === null && $itemAmount !== 0 || $helper->validateDate($syncingFlag);
	}

	/**
	 * @param $syncingFlag
	 * @param $itemAmount
	 * @return bool
	 */
	private function shouldFlagAsSynced($syncingFlag, $itemAmount)
	{
		return ($syncingFlag === '1' || $syncingFlag === null) && $itemAmount === 0;
	}

	/**
	 * @param $mailchimpStoreId
	 * @param $id
	 * @param $type
	 * @return Varien_Object
	 */
	private function getDataProduct($mailchimpStoreId, $id, $type) {return hcg_mc_syncd_get(
		(int)$id, $type, $mailchimpStoreId
	);}

	/**
	 * @param $batchId
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeEcommerce($batchId, $storeId)
	{
		$helper = hcg_mc_h();
		$countersSentPerBatch = $helper->getCountersSentPerBatch();

		if (!empty($countersSentPerBatch) || $countersSentPerBatch != null) {
			$helper->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$helper->logBatchQuantity($helper->getCountersSentPerBatch());
		} else {
			$helper->logBatchStatus("Nothing to sync for store $storeId");
		}
	}

	/**
	 * @param $batchId
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeSubscriber($batchId, $storeId)
	{
		$helper = hcg_mc_h();
		$countersSubscribers = $helper->getCountersSubscribers();

		if (!empty($countersSubscribers) || $helper->getCountersSubscribers() != null) {
			$helper->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$helper->logBatchQuantity($helper->getCountersSubscribers());
		} else {
			$helper->logBatchStatus("Nothing to sync for store $storeId");
		}
	}

	/**
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeDataSentToMailchimp($storeId)
	{
		$helper = hcg_mc_h();
		$countersDataSentToMailchimp = $helper->getCountersDataSentToMailchimp();

		if (!empty($countersDataSentToMailchimp) || $helper->getCountersDataSentToMailchimp() != null) {
			$helper->logBatchStatus("Processed data sent to Mailchimp for store $storeId");
			$counter = $helper->getCountersDataSentToMailchimp();
			$helper->logBatchQuantity($counter);
			if ($this->isSetAnyCounterSubscriberOrEcommerceNotSent($counter)) {
				if ($helper->isErrorLogEnabled()) {
					$helper->logBatchStatus(
						'Please check Mailchimp Errors grid or MailChimp_Errors.log for more details.'
					);
				} else {
					$helper->logBatchStatus(
						'Please check Mailchimp Errors grid and enable MailChimp_Errors.log for more details.'
					);
				}
			}
		} else {
			$helper->logBatchStatus("Nothing was processed for store $storeId");
		}
	}

	/**
	 * @param $counter
	 * @return bool
	 */
	private function isSetAnyCounterSubscriberOrEcommerceNotSent($counter)
	{
		return isset($counter['SUB']['NOT SENT'])
			|| isset($counter['CUS']['NOT SENT'])
			|| isset($counter['ORD']['NOT SENT'])
			|| isset($counter['PRO']['NOT SENT'])
			|| isset($counter['QUO']['NOT SENT']);
	}

	/**
	 * @param Varien_Object $syncDataItem
	 * @return bool
	 */
	private function isFirstArrival(Varien_Object $syncDataItem)
	{
		return (int)$syncDataItem->getMailchimpSyncedFlag() !== 1;
	}

	/**
	 * @param $type
	 * @param Varien_Object $syncDataItem
	 * @return int
	 */
	private function enableMergeFieldsSending($type, Varien_Object $syncDataItem)
	{
		$syncModified = 0;

		if ($type == Ebizmarts_MailChimp_Model_Config::IS_CUSTOMER && $this->isFirstArrival($syncDataItem)) {
			$syncModified = 1;
		}

		return $syncModified;
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_File
	 */
	private function getMailchimpFileHelper()
	{
		return Mage::helper('mailchimp/file');
	}

	/**
	 * Send Subscribers batch on particular store view, return batch response.
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by self::handleSubscriberBatches()
	 */
	private function sendStoreSubscriberBatch(int $sid, int $limit):int {
		$h = hcg_mc_h();
		try {
			if ($h->isSubscriptionEnabled($sid)) {
				$h->resetCountersSubscribers();
				$listId = $h->getGeneralList($sid);
				$batchArray = [];
				$subscribersArray = $this->getApiSubscribers()->createBatchJson($listId, $sid, $limit);
				$limit -= count($subscribersArray);
				$batchArray['operations'] = $subscribersArray;
				if (!empty($batchArray['operations'])) {
					$batchJson = json_encode($batchArray);
					if ($batchJson === false) {
						$h->logRequest('Json encode error ' . json_last_error_msg());
					}
					elseif ($batchJson == '') {
						$h->logRequest('An empty operation was detected');
					}
					else {
						try {
							$mailchimpApi = $h->getApi($sid);
							$batchResponse = $mailchimpApi->getBatchOperation()->add($batchJson);
							$h->logRequest($batchJson, $batchResponse['id']);
							$batch = $this->getSyncBatchesModel();
							$batch->setStoreId($sid)
								->setBatchId($batchResponse['id'])
								->setStatus($batchResponse['status']);
							$batch->save();
							$this->_showResumeSubscriber($batchResponse['id'], $sid);
							return $limit;
						}
						catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
							$h->logError($e->getMessage());
						}
						catch (MailChimp_Error $e) {
							$h->logRequest($batchJson);
							$h->logError($e->getFriendlyMessage());
						}
					}
				}
			}
		}
		catch (Exception $e) {
			# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "`Ebizmarts_MailChimp_Model_Api_Batches::sendStoreSubscriberBatch()` should log the trace for exceptions":
			# https://github.com/thehcginstitute-com/m1/issues/509
			df_log($e);
		}
		return $limit;
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Curl
	 */
	private function getMailchimpCurlHelper() {return Mage::helper('mailchimp/curl'); }

	/**
	 * @return Ebizmarts_MailChimp_Helper_Date
	 */
	private function getDateHelper() {return Mage::helper('mailchimp/date');}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Stores
	 */
	private function getApiStores() {return Mage::getModel('mailchimp/api_stores');}

	const SEND_PROMO_ENABLED = 1;
}