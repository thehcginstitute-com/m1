<?php
# 2024-03-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as SyncD;
class Ebizmarts_MailChimp_Model_Api_ItemSynchronizer
{
	/**
	 * @var Ebizmarts_MailChimp_Helper_Data
	 */
	protected $_mailchimpHelper;

	/**
	 * @var Ebizmarts_MailChimp_Helper_Date
	 */
	protected $_mailchimpDateHelper;

	/**
	 * @return mixed
	 */
	function getMailchimpStoreId()
	{
		return $this->_mailchimpStoreId;
	}

	/**
	 * @param mixed $mailchimpStoreId
	 */
	function setMailchimpStoreId($mailchimpStoreId)
	{
		$this->_mailchimpStoreId = $mailchimpStoreId;
	}

	protected $_mailchimpStoreId;

	protected $_magentoStoreId;

	/**
	 * @return mixed
	 */
	function getMagentoStoreId()
	{
		return $this->_magentoStoreId;
	}

	/**
	 * @param mixed $magentoStoreId
	 */
	function setMagentoStoreId($magentoStoreId)
	{
		$this->_magentoStoreId = $magentoStoreId;
	}

	function __construct()
	{
		$this->_mailchimpHelper = Mage::helper('mailchimp');
		$this->_mailchimpDateHelper = Mage::helper('mailchimp/date');
	}

	/**
	 * @param $id
	 * @param null $syncDelta
	 * @param null $syncError
	 * @param int $syncModified
	 * @param null $syncedFlag
	 * @param null $syncDeleted
	 * @param null $token
	 * @param bool $saveOnlyIfExists
	 * @param bool $allowBatchRemoval
	 * @param int $deletedRelatedId
	 */
	protected function _updateSyncData(
		$id,
		$syncDelta = null,
		$syncError = null,
		$syncModified = 0,
		$syncDeleted = null,
		$syncedFlag = null,
		$token = null,
		$saveOnlyIfExists = false,
		$allowBatchRemoval = true,
		$deletedRelatedId = null
	) {
		$type = $this->getItemType();

		if (!empty($type)) {
			hcg_mc_syncd_new()->saveEcommerceSyncData(
				$id,
				$type,
				$this->getMailchimpStoreId(),
				$syncDelta,
				$syncError,
				$syncModified,
				$syncDeleted,
				$token,
				$syncedFlag,
				$saveOnlyIfExists,
				$deletedRelatedId,
				$allowBatchRemoval
			);
		}
	}

	protected function addDeletedRelatedId($id, $relatedId)
	{
		$this->_updateSyncData(
			$id,
			null,
			null,
			0,
			1,
			null,
			null,
			true,
			false,
			$relatedId
		);
	}

	protected function addSyncDataError(
		$id,
		$error,
		$token = null,
		$saveOnlyIfExists = false,
		$syncDelta = null
	) {
		$type = $this->getItemType();

		$this->logSyncError(
			$error,
			$type,
			$this->getMagentoStoreId(),
			'magento_side_error',
			'Invalid Magento Resource',
			0,
			$id,
			0
		);

		$this->_updateSyncData(
			$id,
			$syncDelta,
			$error,
			0,
			null,
			0,
			$token,
			$saveOnlyIfExists,
			-1
		);
	}

	protected function addSyncData($id)
	{
		$this->_updateSyncData($id);
	}

	protected function addSyncDataToken($id, $token)
	{
		$this->_updateSyncData(
			$id,
			null,
			null,
			0,
			null,
			null,
			$token
		);
	}

	protected function markSyncDataAsModified($id)
	{
		$this->_updateSyncData($id, null, null, 1, null, null, null, true);
	}

	protected function markAllSyncDataAsModified($id)
	{
		$type = $this->getItemType();
		if (!empty($type)) {
			hcg_mc_syncd_new()->markAllAsModified($id,$type);
		}
	}

	protected function markSyncDataAsDeleted($id, $syncedFlag = null)
	{
		$this->_updateSyncData(
			$id,
			null,
			null,
			0,
			1,
			$syncedFlag
		);
	}

	/**
	 * @param $error
	 * @param $type
	 * @param $title
	 * @param $status
	 * @param $originalId
	 * @param $batchId
	 * @param $storeId
	 * @param $regType
	 */
	protected function logSyncError(
		$error,
		$regType,
		$storeId,
		$type,
		$title,
		$status,
		$originalId,
		$batchId
	) {
		# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Log the context for `Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::logSyncError()`":
		# https://github.com/thehcginstitute-com/m1/issues/503
		df_log($error, $this, [
			'batchId' => $batchId
			,'originalId' => $originalId
			,'regType' => $regType
			,'status' => $status
			,'storeId' => $storeId
			,'title' => $title
			,'type' => $type
		]);
		try {
			$this->_logMailchimpError(
				$error, $type, $title,
				$status, $originalId, $batchId, $storeId, $regType
			);
		} catch (Exception $e) {
			$this->getHelper()->logError($e->getMessage());
		}
	}

	/**
	 * @param $error
	 * @param $type
	 * @param $title
	 * @param $status
	 * @param $originalId
	 * @param $batchId
	 * @param $storeId
	 * @param $regType
	 *
	 * @throws Exception
	 */
	protected function _logMailchimpError(
		$error,
		$type,
		$title,
		$status,
		$originalId,
		$batchId,
		$storeId,
		$regType
	) {
		$mailchimpErrors = Mage::getModel('mailchimp/mailchimperrors');

		$mailchimpErrors->setType($type);
		$mailchimpErrors->setTitle($title);
		$mailchimpErrors->setStatus($status);
		$mailchimpErrors->setErrors($error);
		$mailchimpErrors->setRegtype($regType);
		$mailchimpErrors->setOriginalId($originalId);
		$mailchimpErrors->setBatchId($batchId);
		$mailchimpErrors->setStoreId($storeId);
		$mailchimpErrors->setMailchimpStoreId($this->getMailchimpStoreId());

		$mailchimpErrors->save();
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function getHelper($type='')
	{
		return $this->_mailchimpHelper;
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Date
	 */
	protected function getDateHelper()
	{
		return $this->_mailchimpDateHelper;
	}

	/**
	 * @return mixed
	 */
	function getMailchimpEcommerceDataTableName()
	{
		return $this->getCoreResource()
			->getTableName('mailchimp/ecommercesyncdata');
	}

	/**
	 * @param $magentoStoreId
	 * @return mixed
	 */
	function getWebSiteIdFromMagentoStoreId($magentoStoreId)
	{
		return Mage::getModel('core/store')->load($magentoStoreId)->getWebsiteId();
	}

	/**
	 * @return Mage_Core_Model_Resource
	 */
	function getCoreResource()
	{
		return Mage::getSingleton('core/resource');
	}

	/**
	 * @return string
	 */
	protected function getItemType()
	{
		return null;
	}

	function joinMailchimpSyncDataWithoutWhere($customerCollection, $mailchimpStoreId)
	{
		$this->initializeEcommerceResourceCollection()
			->joinMailchimpSyncDataWithoutWhere($customerCollection, $mailchimpStoreId);
	}

	/**
	 * @param $itemType
	 * @param string $where
	 * @param string $isNewItem
	 * @return mixed
	 * @throws Mage_Core_Exception
	 */
	function buildEcommerceCollectionToSync(
		$itemType,
		$where = "m4m.mailchimp_sync_delta IS NULL",
		$isNewItem = "new"
	){
		$collectionToSync = $this->getItemResourceModelCollection();
		$ecommerceResourceCollection = $this->getEcommerceResourceCollection();

		$this->addFilters($collectionToSync, $isNewItem);

		$ecommerceResourceCollection->joinLeftEcommerceSyncData($collectionToSync);

		if ($itemType != Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE) {
			$ecommerceResourceCollection->addWhere(
				$collectionToSync,
				$where,
				$this->getBatchLimitFromConfig()
			);
		} else {
			$ecommerceResourceCollection->addWhere($collectionToSync, $where);
			$collectionToSync->getSelect()->order(array('salesrule.rule_id DESC'));
			// limit the collection
			$ecommerceResourceCollection->limitCollection($collectionToSync, $this->getBatchLimitFromConfig());
		}

		return $collectionToSync;
	}
}
