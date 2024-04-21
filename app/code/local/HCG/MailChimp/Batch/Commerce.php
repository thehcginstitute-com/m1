<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Helper_Data_ApiKeyException as EApiKey;
use Ebizmarts_MailChimp_Model_Api_Stores as ApiStores;
use Ebizmarts_MailChimp_Model_Config as Cfg;
# 2024-04-22 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class Commerce {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by Ebizmarts_MailChimp_Model_Cron::syncEcommerceBatchData()
	 */
	static function p():void {
		$h = hcg_mc_h();
		$stores = \Mage::app()->getStores();
		$h->handleResendDataBefore();
		foreach ($stores as $store) {
			$storeId = $store->getId();
			if ($h->isEcomSyncDataEnabled($storeId)) {
				if ($h->ping($storeId)) {
					GetResults::p($storeId);
					hcg_mc_batches_new()->_sendEcommerceBatch($storeId);
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
			$syncedDateArray = self::addSyncValueToArray($storeId, $syncedDateArray);
		}
		self::handleSyncingValue($syncedDateArray);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param $storeId
	 * @param $syncedDateArray
	 * @return mixed
	 */
	private static function addSyncValueToArray($storeId, $syncedDateArray) {
		$h = hcg_mc_h();
		$ecomEnabled = $h->isEcomSyncDataEnabled($storeId);
		if ($ecomEnabled) {
			$mailchimpStoreId = $h->getMCStoreId($storeId);
			$syncedDate = $h->getMCIsSyncing($mailchimpStoreId, $storeId);
			// Check if $syncedDate is in date format to support previous versions.
			if (isset($syncedDateArray[$mailchimpStoreId]) && $syncedDateArray[$mailchimpStoreId]) {
				if ($h->validateDate($syncedDate)) {
					if ($syncedDate > $syncedDateArray[$mailchimpStoreId]) {
						$syncedDateArray[$mailchimpStoreId] = [$storeId => $syncedDate];
					}
				} elseif ((int)$syncedDate === 1) {
					$syncedDateArray[$mailchimpStoreId] = [$storeId => false];
				}
			}
			else {
				if ($h->validateDate($syncedDate)) {
					$syncedDateArray[$mailchimpStoreId] = [$storeId => $syncedDate];
				}
				else {
					if ((int)$syncedDate === 1 || $syncedDate === null) {
						$syncedDateArray[$mailchimpStoreId] = [$storeId => false];
					}
					elseif (!isset($syncedDateArray[$mailchimpStoreId])) {
						$syncedDateArray[$mailchimpStoreId] = [$storeId => true];
					}
				}
			}
		}
		return $syncedDateArray;
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleEcommerceBatches()
	 * @param $syncedDateArray
	 * @throws \Mage_Core_Exception
	 */
	private static function handleSyncingValue($syncedDateArray):void {
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
						$apiStores = new ApiStores; /** @var ApiStores $apiStores */
						$apiStores->editIsSyncing($api, false, $mailchimpStoreId);
						hcg_mc_cfg_save(Cfg::ECOMMERCE_SYNC_DATE . "_$mailchimpStoreId", $date);
					}
				}
				catch (EApiKey $e) {
					$h->logError($e->getMessage());
				}
				catch (\MailChimp_Error $e) {
					$h->logError($e->getFriendlyMessage());
				}
				catch (\Exception $e) {
					$h->logError($e->getMessage());
				}
			}
		}
	}
}