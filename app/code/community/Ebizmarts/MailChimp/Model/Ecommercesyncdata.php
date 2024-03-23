<?php
# 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
/** @see Ebizmarts_MailChimp_Model_Ecommercesyncdata_Quote */
class Ebizmarts_MailChimp_Model_Ecommercesyncdata extends Mage_Core_Model_Abstract {
	/**
	 * @override
	 * @see Varien_Object::_construct()
	 */
	function _construct():void {$this->_init('mailchimp/ecommercesyncdata');}

	/**
	 * Save entry for ecommerce_sync_data table overwriting old item if exists or creating a new one if it does not.
	 *
	 * @param       $itemId
	 * @param       $itemType
	 * @param       $mailchimpStoreId
	 * @param null  $syncDelta
	 * @param null  $syncError
	 * @param int   $syncModified
	 * @param null  $syncDeleted
	 * @param null  $token
	 * @param null  $syncedFlag
	 * @param bool  $saveOnlyIfexists
	 * @param null  $deletedRelatedId
	 * @param bool  $allowBatchRemoval
	 */
	final function saveEcommerceSyncData(
		$itemId,
		$itemType,
		$mailchimpStoreId,
		$syncDelta = null,
		$syncError = null,
		$syncModified = 0,
		$syncDeleted = null,
		$token = null,
		$syncedFlag = null,
		$saveOnlyIfexists = false,
		$deletedRelatedId = null,
		$allowBatchRemoval = true
	) {
		$ecommerceSyncDataItem = hcg_mc_syncd_get((int)$itemId, $itemType, $mailchimpStoreId);

		if (!$saveOnlyIfexists || $ecommerceSyncDataItem['mailchimp_sync_delta']) {
			$this->setEcommerceSyncDataItemValues(
				$itemId, $itemType, $syncDelta, $syncError, $syncModified, $syncDeleted,
				$token, $syncedFlag, $deletedRelatedId, $allowBatchRemoval, $ecommerceSyncDataItem
			);

			$ecommerceSyncDataItem->save();
		}
	}

	/**
	 * @param $itemId
	 * @param $itemType
	 * @return Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Collection
	 */
	final function getAllEcommerceSyncDataItemsPerId($itemId, $itemType)
	{
		$collection = $this->getCollection()
			->addFieldToFilter('related_id', array('eq' => $itemId))
			->addFieldToFilter('type', array('eq' => $itemType));

		return $collection;
	}

	final function markAllAsModified($id,$type)
	{
		$this->getResource()->markAllAsModified($id,$type);
		return $this;
	}

	/**
	 * 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * 2) https://3v4l.org/NoALo
	 * 3) Returns a string like «2024-03-17 05:53:46» or «0000-00-00 00:00:00».
	 * 4.1) The database field is always filled.
	 * 4.2) It has the `datetime` type.
	 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::ecommerceSentCallback()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::_getPayloadDataLines()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Products::_buildUpdateProductRequest()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Products::sendModifiedProduct()
	 */
	final function time():?string {return $this['mailchimp_sync_delta'];}

	/**
	 * @param $itemId
	 * @param $itemType
	 * @param $syncDelta
	 * @param $syncError
	 * @param $syncModified
	 * @param $syncDeleted
	 * @param $token
	 * @param $syncedFlag
	 * @param $deletedRelatedId
	 * @param $allowBatchRemoval
	 * @param Ebizmarts_MailChimp_Model_Ecommercesyncdata $ecommerceSyncDataItem
	 */
	final protected function setEcommerceSyncDataItemValues(
		$itemId,
		$itemType,
		$syncDelta,
		$syncError,
		$syncModified,
		$syncDeleted,
		$token,
		$syncedFlag,
		$deletedRelatedId,
		$allowBatchRemoval,
		Ebizmarts_MailChimp_Model_Ecommercesyncdata $ecommerceSyncDataItem
	) {
		if ($itemId) {
			$ecommerceSyncDataItem->setData("related_id", $itemId);
		}

		if ($syncDelta) {
			$ecommerceSyncDataItem->setData("mailchimp_sync_delta", $syncDelta);
		} elseif ($allowBatchRemoval === true) {
			$ecommerceSyncDataItem->setData("batch_id", null);
		}

		if ($allowBatchRemoval === -1) {
			$ecommerceSyncDataItem->setData("batch_id", '-1');
		}

		if ($syncError) {
			$ecommerceSyncDataItem->setData("mailchimp_sync_error", $syncError);
		}

		//Always set modified value to 0 when saving sync delta or errors.
		$ecommerceSyncDataItem->setData("mailchimp_sync_modified", $syncModified);

		if ($syncDeleted !== null) {
			$ecommerceSyncDataItem->setData("mailchimp_sync_deleted", $syncDeleted);

			if ($itemType == Ebizmarts_MailChimp_Model_Config::IS_PRODUCT && $syncError == '') {
				$ecommerceSyncDataItem->setData("mailchimp_sync_error", $syncError);
			}
		}

		if ($token) {
			$ecommerceSyncDataItem->setData("mailchimp_token", $token);
		}

		if ($deletedRelatedId) {
			$ecommerceSyncDataItem->setData("deleted_related_id", $deletedRelatedId);
		}

		if ($syncedFlag !== null) {
			$ecommerceSyncDataItem->setData("mailchimp_synced_flag", $syncedFlag);
		}
	}
}