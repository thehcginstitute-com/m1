<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Model_Config as Cfg;
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class SaveSyncData {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by \HCG\MailChimp\Batch\HandleErrorItem::p()
	 * @used-by \HCG\MailChimp\Batch\HandleErrorItem::setItemAsModified()
	 * @used-by \HCG\MailChimp\Batch\ProcessEachResponseFile::p()
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
	static function p(
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
	):void {
		$helper = hcg_mc_h();
		if ($itemType == Cfg::IS_SUBSCRIBER) {
			$helper->updateSubscriberSyndData($itemId, $syncDelta, $syncError, 0, null);
		}
		else {
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
}