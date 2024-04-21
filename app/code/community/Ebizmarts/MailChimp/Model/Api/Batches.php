<?php
# 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
use HCG\MailChimp\Batch\GetResults;
final class Ebizmarts_MailChimp_Model_Api_Batches {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by Ebizmarts_MailChimp_Model_Cron::syncEcommerceBatchData()
	 */
	function handleEcommerceBatches():void {
		$h = hcg_mc_h();
		$stores = Mage::app()->getStores();
		$h->handleResendDataBefore();
		foreach ($stores as $store) {
			$storeId = $store->getId();
			if ($h->isEcomSyncDataEnabled($storeId)) {
				if ($h->ping($storeId)) {
					GetResults::p($this, $storeId);
					$this->_sendEcommerceBatch($storeId);
				}
				else {
					$h->logError(
						"Could not connect to MailChimp: Make sure the API Key is correct "
						. "and there is an internet connection"
					);
					return;
				}
			}
		}
		$h->handleResendDataAfter();
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
			GetResults::p($this, $sid, false);
			if (1 > $limit) {
				break;
			}
			$limit = $this->sendStoreSubscriberBatch($sid, $limit);
		}
		GetResults::p($this, 0, false);
		if (0 < $limit) {
			$this->sendStoreSubscriberBatch(0, $limit);
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
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
		$h = hcg_mc_h();
		$mailchimpApi = $h->getApi($magentoStoreId);
		if (!empty($batchArray['operations'])) {
			$batchJson = json_encode($batchArray);
			if ($batchJson === false) {
				$h->logRequest('Json encode error ' . json_last_error_msg());
			}
			elseif (empty($batchJson)) {
				$h->logRequest('An empty operation was detected');
			}
			else {
				$h->logBatchStatus('Send batch operation');
				$batchResponse = $mailchimpApi->getBatchOperation()->add($batchJson);
				$h->logRequest($batchJson, $batchResponse['id']);
				//save batch id to db
				$batch = new Synchbatches;
				$batch->setStoreId($mailchimpStoreId)->setBatchId($batchResponse['id'])->setStatus($batchResponse['status']);
				$batch->save();
				$this->markItemsAsSent($batchResponse['id'], $mailchimpStoreId);
				$this->_showResumeEcommerce($batchResponse['id'], $magentoStoreId);
			}
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $magentoStoreId");
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 */
	private function _sendEcommerceBatch($magentoStoreId):void {
		$h = hcg_mc_h();
		$mailchimpStoreId = $h->getMCStoreId($magentoStoreId);
		try {
			$this->deleteUnsentItems();

			if ($h->isEcomSyncDataEnabled($magentoStoreId)) {
				$h->resetCountersSentPerBatch();
				$batchArray = array();
				//customer operations
				$h->logBatchStatus('Generate Customers Payload');
				/** @var Ebizmarts_MailChimp_Model_Api_Customers $apiCustomers */
				$apiCustomers = new Ebizmarts_MailChimp_Model_Api_Customers;
				$apiCustomers->setMailchimpStoreId($mailchimpStoreId);
				$apiCustomers->setMagentoStoreId($magentoStoreId);

				$customersArray = $apiCustomers->createBatchJson();
				$batchArray['operations'] = $customersArray;

				//product operations
				$h->logBatchStatus('Generate Products Payload');

				/** @var Ebizmarts_MailChimp_Model_Api_Products $apiProducts */
				$apiProducts = new Ebizmarts_MailChimp_Model_Api_Products;
				$apiProducts->setMailchimpStoreId($mailchimpStoreId);
				$apiProducts->setMagentoStoreId($magentoStoreId);

				$productsArray = $apiProducts->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $productsArray);

				if ($h->getMCIsSyncing($mailchimpStoreId, $magentoStoreId) === 1) {
					$h->logBatchStatus('No Carts will be synced until the store is completely synced');
				} else {
					//cart operations
					$h->logBatchStatus('Generate Carts Payload');
					/** @var Ebizmarts_MailChimp_Model_Api_Carts $apiCarts */
					$apiCarts = new Ebizmarts_MailChimp_Model_Api_Carts;
					$apiCarts->setMailchimpStoreId($mailchimpStoreId);
					$apiCarts->setMagentoStoreId($magentoStoreId);
					$cartsArray = $apiCarts->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $cartsArray);
				}
				//order operations
				$h->logBatchStatus('Generate Orders Payload');
				/** @var Ebizmarts_MailChimp_Model_Api_Orders $apiOrders */
				$apiOrders = new Ebizmarts_MailChimp_Model_Api_Orders;
				$apiOrders->setMailchimpStoreId($mailchimpStoreId);
				$apiOrders->setMagentoStoreId($magentoStoreId);
				$ordersArray = $apiOrders->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $ordersArray);
				if ($h->getPromoConfig($magentoStoreId) == self::SEND_PROMO_ENABLED) {
					//promo rule operations
					$h->logBatchStatus('Generate Promo Rules Payload');
					/** @var Ebizmarts_MailChimp_Model_Api_PromoRules $apiPromoRules */
					$apiPromoRules = new Ebizmarts_MailChimp_Model_Api_PromoRules;
					$apiPromoRules->setMailchimpStoreId($mailchimpStoreId);
					$apiPromoRules->setMagentoStoreId($magentoStoreId);

					$promoRulesArray = $apiPromoRules->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $promoRulesArray);

					//promo code operations
					$h->logBatchStatus('Generate Promo Codes Payload');
					/** @var Ebizmarts_MailChimp_Model_Api_PromoCodes $apiPromoCodes */
					$apiPromoCodes = new Ebizmarts_MailChimp_Model_Api_PromoCodes;
					$apiPromoCodes->setMailchimpStoreId($mailchimpStoreId);
					$apiPromoCodes->setMagentoStoreId($magentoStoreId);

					$promoCodesArray = $apiPromoCodes->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $promoCodesArray);
				}

				//deleted product operations
				$h->logBatchStatus('Generate Deleted Products Payload');
				$deletedProductsArray = $apiProducts->createDeletedProductsBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $deletedProductsArray);
				$batchJson = null;
				$batchResponse = null;

				try {
					$this->_processBatchOperations($batchArray, $mailchimpStoreId, $magentoStoreId);
					$this->_updateSyncingFlag($mailchimpStoreId, $magentoStoreId);
				} catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
					$h->logError($e->getMessage());
				} catch (MailChimp_Error $e) {
					$h->logError($e->getFriendlyMessage());

					if ($batchJson && !isset($batchResponse['id'])) {
						$h->logRequest($batchJson);
					}
				} catch (Exception $e) {
					$h->logError($e->getMessage());
					$h->logError("Json encode fails");
					$h->logError($batchArray);
				}
			}
		} catch (MailChimp_Error $e) {
			$h->logError($e->getFriendlyMessage());
		} catch (Exception $e) {
			$h->logError($e->getMessage());
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_processBatchOperations()
	 * @param $batchId
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeEcommerce($batchId, $storeId):void {
		$h = hcg_mc_h();
		$countersSentPerBatch = $h->getCountersSentPerBatch();
		if (!empty($countersSentPerBatch) || $countersSentPerBatch != null) {
			$h->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$h->logBatchQuantity($h->getCountersSentPerBatch());
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $storeId");
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_sendEcommerceBatch()
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function _updateSyncingFlag($mailchimpStoreId, $magentoStoreId):void {
		$h = hcg_mc_h();
		$itemAmount = $h->getTotalNewItemsSent();
		$syncingFlag = $h->getMCIsSyncing($mailchimpStoreId, $magentoStoreId);
		if ($this->shouldFlagAsSyncing($syncingFlag, $itemAmount, $h)) {
			//Set is syncing per scope in 1 until sync finishes.
			hcg_mc_cfg_save(Cfg::GENERAL_MCISSYNCING . "_$mailchimpStoreId", 1, $magentoStoreId, 'stores');
		}
		elseif ($this->shouldFlagAsSynced($syncingFlag, $itemAmount)) {
			//Set is syncing per scope to a date because it is not sending any more items.
			hcg_mc_cfg_save(
				Cfg::GENERAL_MCISSYNCING . "_$mailchimpStoreId"
				,hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s')
				,$magentoStoreId
				,'stores'
			);
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_sendEcommerceBatch()
	 */
	private function deleteUnsentItems():void {
		$resource = hcg_mc_h()->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = array("batch_id IS NULL AND mailchimp_sync_modified != 1 AND mailchimp_sync_deleted != 1");
		$connection->delete($tableName, $where);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_processBatchOperations()
	 */
	private function markItemsAsSent($batchResponseId, $mailchimpStoreId):void {
		$resource = hcg_mc_h()->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = array("batch_id IS NULL AND mailchimp_store_id = ?" => $mailchimpStoreId);
		$connection->update(
			$tableName,
			array(
				'batch_id' => $batchResponseId,
				'mailchimp_sync_delta' => hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s')
			),
			$where
		);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @param $storeId
	 * @param $syncedDateArray
	 * @return mixed
	 */
	private function addSyncValueToArray($storeId, $syncedDateArray) {
		$h = hcg_mc_h();
		$ecomEnabled = $h->isEcomSyncDataEnabled($storeId);
		if ($ecomEnabled) {
			$mailchimpStoreId = $h->getMCStoreId($storeId);
			$syncedDate = $h->getMCIsSyncing($mailchimpStoreId, $storeId);
			// Check if $syncedDate is in date format to support previous versions.
			if (isset($syncedDateArray[$mailchimpStoreId]) && $syncedDateArray[$mailchimpStoreId]) {
				if ($h->validateDate($syncedDate)) {
					if ($syncedDate > $syncedDateArray[$mailchimpStoreId]) {
						$syncedDateArray[$mailchimpStoreId] = array($storeId => $syncedDate);
					}
				} elseif ((int)$syncedDate === 1) {
					$syncedDateArray[$mailchimpStoreId] = array($storeId => false);
				}
			}
			else {
				if ($h->validateDate($syncedDate)) {
					$syncedDateArray[$mailchimpStoreId] = array($storeId => $syncedDate);
				}
				else {
					if ((int)$syncedDate === 1 || $syncedDate === null) {
						$syncedDateArray[$mailchimpStoreId] = array($storeId => false);
					}
					elseif (!isset($syncedDateArray[$mailchimpStoreId])) {
						$syncedDateArray[$mailchimpStoreId] = array($storeId => true);
					}
				}
			}
		}
		return $syncedDateArray;
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_updateSyncingFlag()
	 * @param $syncingFlag
	 * @param $itemAmount
	 * @param $h
	 */
	private function shouldFlagAsSyncing($syncingFlag, $itemAmount, $h):bool {return
		$syncingFlag === null && $itemAmount !== 0 || $h->validateDate($syncingFlag)
	;}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_updateSyncingFlag()
	 * @param $syncingFlag
	 * @param $itemAmount
	 */
	private function shouldFlagAsSynced($syncingFlag, $itemAmount):bool {return
		($syncingFlag === '1' || $syncingFlag === null) && $itemAmount === 0
	;}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::sendStoreSubscriberBatch()
	 * @param $batchId
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeSubscriber($batchId, $storeId):void	{
		$h = hcg_mc_h();
		$countersSubscribers = $h->getCountersSubscribers();
		if (!empty($countersSubscribers) || $h->getCountersSubscribers() != null) {
			$h->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$h->logBatchQuantity($h->getCountersSubscribers());
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $storeId");
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @param $syncedDateArray
	 * @throws Mage_Core_Exception
	 */
	private function handleSyncingValue($syncedDateArray):void {
		$h = hcg_mc_h();
		foreach ($syncedDateArray as $mailchimpStoreId => $val) {
			$magentoStoreId = key($val);
			$date = $val[$magentoStoreId];
			$ecomEnabled = $h->isEcomSyncDataEnabled($magentoStoreId);
			if ($ecomEnabled && $date) {
				try {
					$api = $h->getApi($magentoStoreId);
					$isSyncingDate = $h->getDateSyncFinishByMailChimpStoreId($mailchimpStoreId);
					if (!$isSyncingDate && $mailchimpStoreId) {
						$apiStores = new Ebizmarts_MailChimp_Model_Api_Stores;
						$apiStores->editIsSyncing($api, false, $mailchimpStoreId);
						hcg_mc_cfg_save(Cfg::ECOMMERCE_SYNC_DATE . "_$mailchimpStoreId", $date);
					}
				} catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
					$h->logError($e->getMessage());
				} catch (MailChimp_Error $e) {
					$h->logError($e->getFriendlyMessage());
				} catch (Exception $e) {
					$h->logError($e->getMessage());
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