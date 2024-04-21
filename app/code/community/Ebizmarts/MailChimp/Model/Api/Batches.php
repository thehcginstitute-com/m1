<?php
# 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
# 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
use HCG\MailChimp\Batch\HandleErrorItem;
use HCG\MailChimp\Batch\SaveSyncData;
final class Ebizmarts_MailChimp_Model_Api_Batches {
	/**
	 * 2024-04-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()`":
	 * https://github.com/thehcginstitute-com/m1/issues/571
	 * @used-by self::_getResults()
	 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::downloadresponseAction()
	 * @param $batchId
	 * @param $magentoStoreId
	 */
	function getBatchResponse($batchId, $magentoStoreId):array {
		$helper = hcg_mc_h();
		$fileHelper = $this->getMailchimpFileHelper();
		$r = []; /** @var array $r */
		try {
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
					$curlOptions = [
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FILE => $fd,
						CURLOPT_FOLLOWLOCATION => true, // this will follow redirects
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					];
					$curlHelper = $this->getMailchimpCurlHelper();
					$curlHelper->curlExec($fileUrl, Zend_Http_Client::GET, $curlOptions);
					fclose($fd);
					$fileHelper->mkDir(hcg_mc_batches_path($batchId), 0750, true);
					$archive = new Mage_Archive();
					if ($fileHelper->fileExists($fileName)) {
						$r = $this->_unpackBatchFile($r, $batchId, $archive, $fileName);
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
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by Ebizmarts_MailChimp_Model_Cron::syncEcommerceBatchData()
	 */
	function handleEcommerceBatches():void {
		$helper = hcg_mc_h();
		$stores = Mage::app()->getStores();
		$helper->handleResendDataBefore();
		foreach ($stores as $store) {
			$storeId = $store->getId();
			if ($helper->isEcomSyncDataEnabled($storeId)) {
				if ($helper->ping($storeId)) {
					$this->_getResults($storeId);
					$this->_sendEcommerceBatch($storeId);
				}
				else {
					$helper->logError(
						"Could not connect to MailChimp: Make sure the API Key is correct "
						. "and there is an internet connection"
					);
					return;
				}
			}
		}
		$helper->handleResendDataAfter();
		$syncedDateArray = [];
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
		foreach (Mage::app()->getStores() as $s) {
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
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @used-by self::handleSubscriberBatches()
	 * @used-by self::replaceAllOrders()
	 * @used-by self::STUB()
	 * @param $magentoStoreId
	 * @param bool  $isEcommerceData
	 * @throws Mage_Core_Exception
	 */
	private function _getResults(
		$magentoStoreId, $isEcommerceData = true, $status = Ebizmarts_MailChimp_Helper_Data::BATCH_PENDING
	) {
		$helper = hcg_mc_h();
		$mailchimpStoreId = $helper->getMCStoreId($magentoStoreId);
		$sb = new Synchbatches;
		$collection = $sb->getCollection()->addFieldToFilter('status', array('eq' => $status));

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
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_sendEcommerceBatch()
	 * @param $batchArray
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Ebizmarts_MailChimp_Helper_Data_ApiKeyException
	 * @throws Mage_Core_Exception
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	private function _processBatchOperations($batchArray, $mailchimpStoreId, $magentoStoreId):void {
		$helper = hcg_mc_h();
		$mailchimpApi = $helper->getApi($magentoStoreId);
		if (!empty($batchArray['operations'])) {
			$batchJson = json_encode($batchArray);
			if ($batchJson === false) {
				$helper->logRequest('Json encode error ' . json_last_error_msg());
			}
			elseif (empty($batchJson)) {
				$helper->logRequest('An empty operation was detected');
			}
			else {
				$helper->logBatchStatus('Send batch operation');
				$batchResponse = $mailchimpApi->getBatchOperation()->add($batchJson);
				$helper->logRequest($batchJson, $batchResponse['id']);
				//save batch id to db
				$batch = new Synchbatches;
				$batch->setStoreId($mailchimpStoreId)->setBatchId($batchResponse['id'])->setStatus($batchResponse['status']);
				$batch->save();
				$this->markItemsAsSent($batchResponse['id'], $mailchimpStoreId);
				$this->_showResumeEcommerce($batchResponse['id'], $magentoStoreId);
			}
		}
		else {
			$helper->logBatchStatus("Nothing to sync for store $magentoStoreId");
		}
	}

	/**
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 */
	private function _sendEcommerceBatch($magentoStoreId):void {
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

				/** @var Ebizmarts_MailChimp_Model_Api_Products $apiProducts */
				$apiProducts = new Ebizmarts_MailChimp_Model_Api_Products;
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
				/** @var Ebizmarts_MailChimp_Model_Api_Orders $apiOrders */
				$apiOrders = new Ebizmarts_MailChimp_Model_Api_Orders;
				$apiOrders->setMailchimpStoreId($mailchimpStoreId);
				$apiOrders->setMagentoStoreId($magentoStoreId);
				$ordersArray = $apiOrders->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $ordersArray);
				if ($helper->getPromoConfig($magentoStoreId) == self::SEND_PROMO_ENABLED) {
					//promo rule operations
					$helper->logBatchStatus('Generate Promo Rules Payload');
					/** @var Ebizmarts_MailChimp_Model_Api_PromoRules $apiPromoRules */
					$apiPromoRules = new Ebizmarts_MailChimp_Model_Api_PromoRules;
					$apiPromoRules->setMailchimpStoreId($mailchimpStoreId);
					$apiPromoRules->setMagentoStoreId($magentoStoreId);

					$promoRulesArray = $apiPromoRules->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $promoRulesArray);

					//promo code operations
					$helper->logBatchStatus('Generate Promo Codes Payload');
					/** @var Ebizmarts_MailChimp_Model_Api_PromoCodes $apiPromoCodes */
					$apiPromoCodes = new Ebizmarts_MailChimp_Model_Api_PromoCodes;
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
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_processBatchOperations()
	 * @param $batchId
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeEcommerce($batchId, $storeId):void {
		$helper = hcg_mc_h();
		$countersSentPerBatch = $helper->getCountersSentPerBatch();
		if (!empty($countersSentPerBatch) || $countersSentPerBatch != null) {
			$helper->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$helper->logBatchQuantity($helper->getCountersSentPerBatch());
		}
		else {
			$helper->logBatchStatus("Nothing to sync for store $storeId");
		}
	}

	/**
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::getBatchResponse()
	 * @param $files
	 * @param $batchId
	 * @param $fileName
	 */
	private function _unpackBatchFile($files, $batchId, Mage_Archive $archive, $fileName):array {
		$path = hcg_mc_batches_path($batchId);
		$archive->unpack($fileName, $path);
		$archive->unpack($path . DS . $batchId . '.tar', $path);
		$fileHelper = $this->getMailchimpFileHelper();
		$dirItems = new DirectoryIterator($path);
		foreach ($dirItems as $dirItem) {
			if ($dirItem->isFile() && $dirItem->getExtension() == 'json') {
				$files[] = $path . DS . $dirItem->getBasename();
			}
		}
		$fileHelper->rm($path . DS . $batchId . '.tar');
		$fileHelper->rm($fileName);
		return $files;
	}

	/**
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_sendEcommerceBatch()
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function _updateSyncingFlag($mailchimpStoreId, $magentoStoreId):void {
		$helper = hcg_mc_h();
		$dateHelper = $this->getDateHelper();
		$itemAmount = $helper->getTotalNewItemsSent();
		$syncingFlag = $helper->getMCIsSyncing($mailchimpStoreId, $magentoStoreId);
		if ($this->shouldFlagAsSyncing($syncingFlag, $itemAmount, $helper)) {
			//Set is syncing per scope in 1 until sync finishes.
			hcg_mc_cfg_save(Cfg::GENERAL_MCISSYNCING . "_$mailchimpStoreId", 1, $magentoStoreId, 'stores');
		}
		elseif ($this->shouldFlagAsSynced($syncingFlag, $itemAmount)) {
			//Set is syncing per scope to a date because it is not sending any more items.
			hcg_mc_cfg_save(
				Cfg::GENERAL_MCISSYNCING . "_$mailchimpStoreId"
				,$dateHelper->formatDate(null, 'Y-m-d H:i:s')
				,$magentoStoreId
				,'stores'
			);
		}
	}

	/**
	 * @param $batchId
	 */
	private function deleteBatchItems($batchId) {
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
						HandleErrorItem::p($item, $batchId, $mailchimpStoreId, $id, $type, $store);
					}
					else {
						$syncDataItem = hcg_mc_syncd_get((int)$id, $type, $mailchimpStoreId);
						if (!$syncDataItem->getMailchimpSyncModified()) {
							$syncModified = $this->enableMergeFieldsSending($type, $syncDataItem);
							SaveSyncData::p(
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
						}
						else {
							SaveSyncData::p(
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
	 * @param $item
	 * @param $files
	 * @param $batchId
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 */
	private function _saveItemStatus($item, $files, $batchId, $mailchimpStoreId, $magentoStoreId):void {
		$helper = hcg_mc_h();
		if (!empty($files)) {
			if (isset($files['error'])) {
				$item->setStatus('error');
				$item->save();
				$helper->logBatchStatus('There was an error getting the result ');
			}
			else {
				$this->processEachResponseFile($files, $batchId, $mailchimpStoreId, $magentoStoreId);
				$item->setStatus('completed');
				$item->save();
			}
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

		if ($type == Cfg::IS_CUSTOMER && $this->isFirstArrival($syncDataItem)) {
			$syncModified = 1;
		}

		return $syncModified;
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Stores
	 */
	private function getApiStores() {return Mage::getModel('mailchimp/api_stores');}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Date
	 */
	private function getDateHelper() {return Mage::helper('mailchimp/date');}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Curl
	 */
	private function getMailchimpCurlHelper() {return Mage::helper('mailchimp/curl'); }

	/**
	 * @return Ebizmarts_MailChimp_Helper_File
	 */
	private function getMailchimpFileHelper()
	{
		return Mage::helper('mailchimp/file');
	}

	/**
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @param $syncedDateArray
	 * @throws Mage_Core_Exception
	 */
	private function handleSyncingValue($syncedDateArray):void {
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
						hcg_mc_cfg_save(Cfg::ECOMMERCE_SYNC_DATE . "_$mailchimpStoreId", $date);
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
				/** @var Ebizmarts_MailChimp_Model_Api_Subscribers $api */
				$api = new Ebizmarts_MailChimp_Model_Api_Subscribers;
				$subscribersArray = $api->createBatchJson($listId, $sid, $limit);
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
							$batch = new Synchbatches;
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

	const SEND_PROMO_ENABLED = 1;
}