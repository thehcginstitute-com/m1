<?php
namespace HCG\MailChimp\Batch\Commerce;
use Ebizmarts_MailChimp_Helper_Data_ApiKeyException as EApiKey;
use Ebizmarts_MailChimp_Model_Api_Carts as ApiCarts;
use Ebizmarts_MailChimp_Model_Api_Customers as ApiCustomers;
use Ebizmarts_MailChimp_Model_Api_Orders as ApiOrders;
use Ebizmarts_MailChimp_Model_Api_Products as ApiProducts;
use Ebizmarts_MailChimp_Model_Api_PromoCodes as ApiPromoCodes;
use Ebizmarts_MailChimp_Model_Api_PromoRules as ApiPromoRules;
use Ebizmarts_MailChimp_Model_Config as Cfg;
# 2024-04-22 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class Send {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by \HCG\MailChimp\Batch\Commerce::p()
	 * @param $magentoStoreId
	 * @throws \Mage_Core_Exception
	 */
	static function p($magentoStoreId):void {
		$h = hcg_mc_h();
		$mailchimpStoreId = $h->getMCStoreId($magentoStoreId);
		try {
			self::deleteUnsentItems();
			if ($h->isEcomSyncDataEnabled($magentoStoreId)) {
				$h->resetCountersSentPerBatch();
				$batchArray = [];
				//customer operations
				$h->logBatchStatus('Generate Customers Payload');
				$apiCustomers = new ApiCustomers; /** @var ApiCustomers $apiCustomers */
				$apiCustomers->setMailchimpStoreId($mailchimpStoreId);
				$apiCustomers->setMagentoStoreId($magentoStoreId);
				$customersArray = $apiCustomers->createBatchJson();
				$batchArray['operations'] = $customersArray;
				//product operations
				$h->logBatchStatus('Generate Products Payload');
				$apiProducts = new ApiProducts; /** @var ApiProducts $apiProducts */
				$apiProducts->setMailchimpStoreId($mailchimpStoreId);
				$apiProducts->setMagentoStoreId($magentoStoreId);
				$productsArray = $apiProducts->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $productsArray);

				if ($h->getMCIsSyncing($mailchimpStoreId, $magentoStoreId) === 1) {
					$h->logBatchStatus('No Carts will be synced until the store is completely synced');
				}
				else {
					//cart operations
					$h->logBatchStatus('Generate Carts Payload');
					$apiCarts = new ApiCarts; /** @var ApiCarts $apiCarts */
					$apiCarts->setMailchimpStoreId($mailchimpStoreId);
					$apiCarts->setMagentoStoreId($magentoStoreId);
					$cartsArray = $apiCarts->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $cartsArray);
				}
				//order operations
				$h->logBatchStatus('Generate Orders Payload');
				$apiOrders = new ApiOrders; /** @var ApiOrders $apiOrders */
				$apiOrders->setMailchimpStoreId($mailchimpStoreId);
				$apiOrders->setMagentoStoreId($magentoStoreId);
				$ordersArray = $apiOrders->createBatchJson();
				$batchArray['operations'] = array_merge($batchArray['operations'], $ordersArray);
				if ($h->getPromoConfig($magentoStoreId) == 1) {
					//promo rule operations
					$h->logBatchStatus('Generate Promo Rules Payload');
					$apiPromoRules = new ApiPromoRules; /** @var ApiPromoRules $apiPromoRules */
					$apiPromoRules->setMailchimpStoreId($mailchimpStoreId);
					$apiPromoRules->setMagentoStoreId($magentoStoreId);
					$promoRulesArray = $apiPromoRules->createBatchJson();
					$batchArray['operations'] = array_merge($batchArray['operations'], $promoRulesArray);
					//promo code operations
					$h->logBatchStatus('Generate Promo Codes Payload');
					$apiPromoCodes = new ApiPromoCodes; /** @var ApiPromoCodes $apiPromoCodes */
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
					hcg_mc_batches_new()->_processBatchOperations($batchArray, $mailchimpStoreId, $magentoStoreId);
					self::_updateSyncingFlag($mailchimpStoreId, $magentoStoreId);
				}
				catch (EApiKey $e) {
					$h->logError($e->getMessage());
				}
				catch (\MailChimp_Error $e) {
					$h->logError($e->getFriendlyMessage());

					if ($batchJson && !isset($batchResponse['id'])) {
						$h->logRequest($batchJson);
					}
				}
				catch (\Exception $e) {
					$h->logError($e->getMessage());
					$h->logError("Json encode fails");
					$h->logError($batchArray);
				}
			}
		}
		catch (\MailChimp_Error $e) {
			$h->logError($e->getFriendlyMessage());
		}
		catch (\Exception $e) {
			$h->logError($e->getMessage());
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 */
	private static function deleteUnsentItems():void {
		$resource = hcg_mc_h()->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = ["batch_id IS NULL AND mailchimp_sync_modified != 1 AND mailchimp_sync_deleted != 1"];
		$connection->delete($tableName, $where);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_updateSyncingFlag()
	 * @param $syncingFlag
	 * @param $itemAmount
	 */
	private static function shouldFlagAsSynced($syncingFlag, $itemAmount):bool {return
		($syncingFlag === '1' || $syncingFlag === null) && $itemAmount === 0
	;}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_updateSyncingFlag()
	 * @param $syncingFlag
	 * @param $itemAmount
	 * @param $h
	 */
	private static function shouldFlagAsSyncing($syncingFlag, $itemAmount, $h):bool {return
		$syncingFlag === null && $itemAmount !== 0 || $h->validateDate($syncingFlag)
	;}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws \Mage_Core_Exception
	 * @throws \Mage_Core_Model_Store_Exception
	 */
	private static function _updateSyncingFlag($mailchimpStoreId, $magentoStoreId):void {
		$h = hcg_mc_h();
		$itemAmount = $h->getTotalNewItemsSent();
		$syncingFlag = $h->getMCIsSyncing($mailchimpStoreId, $magentoStoreId);
		if (self::shouldFlagAsSyncing($syncingFlag, $itemAmount, $h)) {
			//Set is syncing per scope in 1 until sync finishes.
			hcg_mc_cfg_save(Cfg::GENERAL_MCISSYNCING . "_$mailchimpStoreId", 1, $magentoStoreId, 'stores');
		}
		elseif (self::shouldFlagAsSynced($syncingFlag, $itemAmount)) {
			//Set is syncing per scope to a date because it is not sending any more items.
			hcg_mc_cfg_save(
				Cfg::GENERAL_MCISSYNCING . "_$mailchimpStoreId"
				,hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s')
				,$magentoStoreId
				,'stores'
			);
		}
	}
}