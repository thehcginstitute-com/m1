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
					Commerce\Send::p($storeId);
				}
				else {
					df_log(
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
	 * @param $syncedDateArray
	 * @return mixed
	 */
	private static function addSyncValueToArray(int $storeId, $syncedDateArray) {
		$h = hcg_mc_h();
		$ecomEnabled = $h->isEcomSyncDataEnabled($storeId);
		if ($ecomEnabled) {
			$mcStore = hcg_mc_sid($storeId); /** @var string $mcStore */
			$syncedDate = $h->getMCIsSyncing($mcStore, $storeId);
			// Check if $syncedDate is in date format to support previous versions.
			if (isset($syncedDateArray[$mcStore]) && $syncedDateArray[$mcStore]) {
				if ($h->validateDate($syncedDate)) {
					if ($syncedDate > $syncedDateArray[$mcStore]) {
						$syncedDateArray[$mcStore] = [$storeId => $syncedDate];
					}
				} elseif ((int)$syncedDate === 1) {
					$syncedDateArray[$mcStore] = [$storeId => false];
				}
			}
			else {
				if ($h->validateDate($syncedDate)) {
					$syncedDateArray[$mcStore] = [$storeId => $syncedDate];
				}
				else {
					if ((int)$syncedDate === 1 || $syncedDate === null) {
						$syncedDateArray[$mcStore] = [$storeId => false];
					}
					elseif (!isset($syncedDateArray[$mcStore])) {
						$syncedDateArray[$mcStore] = [$storeId => true];
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
		foreach ($syncedDateArray as $mcStore => $val) {
			$mgStore = (int)key($val); /** @var int $mgStore */
			$date = $val[$mgStore];
			$ecomEnabled = $h->isEcomSyncDataEnabled($mgStore);
			if ($ecomEnabled && $date) {
				try {
					$api = $h->getApi($mgStore);
					$isSyncingDate = $h->getDateSyncFinishByMailChimpStoreId($mcStore);
					if (!$isSyncingDate && $mcStore) {
						$apiStores = new ApiStores; /** @var ApiStores $apiStores */
						$apiStores->editIsSyncing($api, false, $mcStore);
						hcg_mc_cfg_save(Cfg::ECOMMERCE_SYNC_DATE . "_$mcStore", $date);
					}
				}
				catch (\Exception $e) {df_log($e);}
			}
		}
	}
}