<?php
use HCG\MailChimp\Tags;
use Mage_Newsletter_Model_Subscriber as Sub;
# 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
class Ebizmarts_MailChimp_Model_Api_Subscribers {
	const BATCH_LIMIT = 100;

	/**
	 * Ebizmarts_MailChimp_Helper_Data
	 */
	protected $_mcHelper;
	protected $_storeId;

	/**
	 * @var $_ecommerceSubscribersCollection Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Subscribers_Collection
	 */
	protected $_ecommerceSubscribersCollection;

	function __construct()
	{
		$mageMCHelper = hcg_mc_h();
		$this->setMailchimpHelper($mageMCHelper);
	}

	/**
	 * @param $storeId
	 */
	protected function setStoreId($storeId)
	{
		$this->_storeId = $storeId;
	}

	/**
	 * @return mixed
	 */
	function getStoreId()
	{
		return $this->_storeId;
	}

	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @param $listId
	 * @return array
	 * @throws Mage_Core_Exception
	 * @throws Mage_Core_Model_Store_Exception
	 */
	function createBatchJson($listId, int $storeId, int $limit) {
		$this->setStoreId($storeId);
		$h = hcg_mc_h();
		$thisScopeHasSubMinSyncDateFlag = $h->getIfConfigExistsForScope(
			Ebizmarts_MailChimp_Model_Config::GENERAL_SUBMINSYNCDATEFLAG, $this->getStoreId()
		);
		$thisScopeHasList = $h->getIfConfigExistsForScope(
			Ebizmarts_MailChimp_Model_Config::GENERAL_LIST, $this->getStoreId()
		);
		$this->_ecommerceSubscribersCollection = $this->getResourceCollection();
		$this->_ecommerceSubscribersCollection->setStoreId($this->getStoreId());
		$subscriberArray = array();
		if ($thisScopeHasList && !$thisScopeHasSubMinSyncDateFlag || !$h->getSubMinSyncDateFlag($this->getStoreId())) {
			$realScope = hcg_mc_cfg_scope(
				Ebizmarts_MailChimp_Model_Config::GENERAL_LIST,
				$this->getStoreId()
			);
			hcg_mc_cfg_save(
				Ebizmarts_MailChimp_Model_Config::GENERAL_SUBMINSYNCDATEFLAG
				,hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s')
				# 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# «`Ebizmarts_MailChimp`: «Column 'scope' cannot be null, query was:
				# INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES (?, ?, ?, ?)»:
				# https://github.com/thehcginstitute-com/m1/issues/508
				,dfa($realScope, 'scope_id', 0)
				,dfa($realScope, 'scope', 'default')
			);
		}
		//get subscribers
		$collection = Mage::getResourceModel('newsletter/subscriber_collection')
			->addFieldToFilter('subscriber_status', ['eq' => 1])
			->addFieldToFilter('store_id', ['eq' => $this->getStoreId()])
			->addFieldToFilter(
				[
					'mailchimp_sync_delta',
					'mailchimp_sync_delta',
					'mailchimp_sync_delta',
					'mailchimp_sync_modified'
				],
				[
					['null' => true]
					# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
					# 1) "`Ebizmarts_MailChimp`: «Incorrect DATETIME value: '', query was:
					# SELECT `main_table`.*
					# FROM `newsletter_subscriber` AS `main_table`
					# WHERE (`subscriber_status` = 1)
					# AND (main_table.store_id = '1')
					# AND (
					# 		(`mailchimp_sync_delta` IS NULL)
					# 		OR (`mailchimp_sync_delta` = '')
					# 		OR (`mailchimp_sync_delta` < '2024-04-13 20:30:38')
					# 		OR (`mailchimp_sync_modified` = 1)
					# )
					# AND (`mailchimp_sync_error` = '')
					# LIMIT 200;»":
					# https://github.com/thehcginstitute-com/m1/issues/563
					# 2) https://stackoverflow.com/a/62230447
					# 3) https://stackoverflow.com/a/77660311
					,['eq' => '0000-00-00 00:00:00']
					,['lt' => $h->getSubMinSyncDateFlag($this->getStoreId())]
					,['eq' => 1]
				]
			);
		$collection->addFieldToFilter('mailchimp_sync_error', array('eq' => ''));
		$this->_ecommerceSubscribersCollection->limitCollection($collection, $limit);
		$date = hcg_mc_h_date()->getDateMicrotime();
		$batchId = 'storeid-' . $this->getStoreId() . '_'
			. Ebizmarts_MailChimp_Model_Config::IS_SUBSCRIBER . '_' . $date;
		$counter = 0;
		foreach ($collection as $subscriber) {
			$data = $this->_buildSubscriberData($subscriber);
			$emailHash = hash('md5', strtolower($subscriber->getSubscriberEmail()));

			//encode to JSON
			$subscriberJson = json_encode($data);

			if ($subscriberJson !== false) {
				if (!empty($subscriberJson)) {
					if ($subscriber->getMailchimpSyncModified()) {
						$h->modifyCounterSubscribers(Ebizmarts_MailChimp_Helper_Data::SUB_MOD);
					} else {
						$h->modifyCounterSubscribers(Ebizmarts_MailChimp_Helper_Data::SUB_NEW);
					}

					$subscriberArray[$counter]['method'] = "PUT";
					$subscriberArray[$counter]['path'] = "/lists/" . $listId . "/members/" . $emailHash;
					$subscriberArray[$counter]['operation_id'] = $batchId . '_' . $subscriber->getSubscriberId();
					$subscriberArray[$counter]['body'] = $subscriberJson;

					$this->_saveSubscriber(
						$subscriber,
						'',
						hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s'),
						true
					);
				}
			}
			else {
				//json encode failed
				$jsonErrorMsg = json_last_error_msg();
				df_log("Subscriber " . $subscriber->getSubscriberId() . " json encode failed (" . $jsonErrorMsg . ")");
				$this->_saveSubscriber($subscriber, $jsonErrorMsg);
			}
			$counter++;
		}
		return $subscriberArray;
	}

	/**
	 * @param $subscriber
	 * @param $error
	 * @param null $syncDelta
	 * @param bool $setSource
	 */
	protected function _saveSubscriber($subscriber, $error, $syncDelta = null, $setSource = false)
	{
		if ($setSource) {
			$subscriber->setSubscriberSource(Ebizmarts_MailChimp_Model_Subscriber::MAILCHIMP_SUBSCRIBE);
		}

		$subscriber->setData("mailchimp_sync_delta", $syncDelta);
		$subscriber->setData("mailchimp_sync_error", $error);
		$subscriber->setData("mailchimp_sync_modified", 0);
		$subscriber->save();
	}

	/**
	 * 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 */
	protected function _buildSubscriberData(Sub $s):array {
		$helper = $this->getMailchimpHelper();
		$storeId = $s->getStoreId();
		$r = [];
		$r["email_address"] = $s->getSubscriberEmail();
		if ($t = Tags::p($s, (int)$storeId)) {
			$r["merge_fields"] = $t;
		}
		$status = $this->translateMagentoStatusToMailchimpStatus($s->getStatus());
		$r["status_if_new"] = $status;
		if ($s->getMailchimpSyncModified()) {
			$r["status"] = $status;
		}
		$r["language"] = $helper->getStoreLanguageCode($storeId);
		$interest = $this->_getInterest($s);
		if (!empty($interest)) {
			$r['interests'] = $interest;
		}
		return $r;
	}

	/**
	 * @param $subscriber
	 * @return array
	 * @throws Mage_Core_Exception
	 * @throws MailChimp_Error
	 */
	protected function _getInterest($subscriber)
	{
		$storeId = $subscriber->getStoreId();
		$rc = array();
		$helper = $this->getMailchimpHelper();
		$interestsAvailable = $helper->getInterest($storeId);
		$interest = $helper->getInterestGroups(null, $subscriber->getSubscriberId(), $storeId, $interestsAvailable);

		foreach ($interest as $i) {
			if (array_key_exists('category', $i)) {
				foreach ($i['category'] as $key => $value) {
					$rc[$value['id']] = $value['checked'];
				}
			}
		}

		return $rc;
	}

	/**
	 * 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 */
	function updateSubscriber(Sub $s, $updateStatus = false) {
		$saveSubscriber = false;
		$isAdmin = Mage::app()->getStore()->isAdmin();
		$helper = $this->getMailchimpHelper();
		$storeId = $s->getStoreId();
		$subscriptionEnabled = $helper->isSubscriptionEnabled($storeId);
		if ($subscriptionEnabled) {
			$listId = $helper->getGeneralList($storeId);
			$newStatus = $this->translateMagentoStatusToMailchimpStatus($s->getStatus());
			$forceStatus = ($updateStatus) ? $newStatus : null;
			try {
				$api = $helper->getApi($storeId);
			}
			catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
				df_log($e);
				return;
			}
			$language = $helper->getStoreLanguageCode($storeId);
			$interest = $this->_getInterest($s);
			$emailHash = hash('md5', strtolower($s->getSubscriberEmail()));
			$t = Tags::p($s, (int)$storeId); /** @var array $t */
			try {
				$api->lists->members->addOrUpdate(
					$listId,
					$emailHash,
					$s->getSubscriberEmail(),
					$newStatus,
					null,
					$forceStatus,
					$t,
					$interest,
					$language,
					null,
					null
				);
				$s->setData("mailchimp_sync_delta", hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s'));
				$s->setData("mailchimp_sync_error", "");
				$s->setData("mailchimp_sync_modified", 0);
				$saveSubscriber = true;
			} catch (MailChimp_Error $e) {
				if ($this->isSubscribed($newStatus) && $s->getIsStatusChanged()
					&& !$helper->isSubscriptionConfirmationEnabled($storeId)
				) {
					if (strstr($e->getMailchimpDetails(), 'is in a compliance state')) {
						try {
							$this->_catchMailchimpNewstellerConfirm(
								$api, $listId, $emailHash, $t, $s, $interest
							);
							$saveSubscriber = true;
						}
						catch (MailChimp_Error $e) {
							$this->_catchMailchimpException($e, $s, $isAdmin);
							$saveSubscriber = true;
						}
						catch (Exception $e) {df_log($e);}
					}
					else {
						$this->_catchMailchimpSubsNotAppliedIf($e, $isAdmin, $s);
						$saveSubscriber = true;
					}
				} else {
					$this->_catchMailchimpSubsNotAppliedElse($e, $isAdmin, $s);
				}
			}
			catch (Exception $e) {df_log($e);}
			if ($saveSubscriber) {
				$s->setSubscriberSource(Ebizmarts_MailChimp_Model_Subscriber::MAILCHIMP_SUBSCRIBE);
				$s->save();
			}
		}
	}

	/**
	 * @param $e
	 * @param $isAdmin
	 * @param $subscriber
	 */
	protected function _catchMailchimpSubsNotAppliedIf($e, $isAdmin, $subscriber) {
		$helper = $this->getMailchimpHelper();
		$errorMessage = df_xts($e);
		df_log($e);
		if ($isAdmin) {
			$this->addError($errorMessage);
		}
		else {
			$errorMessage = $helper->__("The subscription could not be applied.");
			$this->addError($errorMessage);
		}
		$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
	}

	/**
	 * @param $e
	 * @param $isAdmin
	 * @param $subscriber
	 */
	protected function _catchMailchimpSubsNotAppliedElse($e, $isAdmin, $subscriber) {
		$helper = $this->getMailchimpHelper();
		$errorMessage = df_xts($e);
		df_log($e);
		if ($isAdmin) {
			$this->addError($errorMessage);
		} else {
			$errorMessage = $helper->__("The subscription could not be applied.");
			$this->addError($errorMessage);
		}
		$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
	}

	/**
	 * @param $api
	 * @param $listId
	 * @param $emailHash
	 * @param $subscriber
	 * @param $interest
	 */
	protected function _catchMailchimpNewstellerConfirm(
		$api,
		$listId,
		$emailHash,
		array $mailChimpTags,
		$subscriber,
		$interest
	) {
		$helper = $this->getMailchimpHelper();
		$api->getLists()->getMembers()->update($listId, $emailHash, null, 'pending', $mailChimpTags, $interest);
		$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE);
		$message = $helper->__('To begin receiving the newsletter, you must first confirm your subscription');
		Mage::getSingleton('core/session')->addWarning($message);
	}

	/**
	 * @param $e
	 * @param $subscriber
	 * @param $isAdmin
	 */
	protected function _catchMailchimpException($e, $subscriber, $isAdmin) {
		$helper = $this->getMailchimpHelper();
		$errorMessage = df_xts($e);
		df_log($e);
		if ($isAdmin) {
			$this->addError($errorMessage);
		}
		else {
			$errorMessage = $helper->__("The subscription could not be applied.");
			$this->addError($errorMessage);
		}
		$subscriber->setSubscriberStatus(Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED);
	}

	/**
	 * @param $status
	 * @return string
	 */
	function translateMagentoStatusToMailchimpStatus($status)
	{
		if ($this->statusEqualsUnsubscribed($status)) {
			$status = 'unsubscribed';
		} elseif ($this->statusEqualsNotActive($status) || $this->statusEqualsUnconfirmed($status)) {
			$status = 'pending';
		} elseif ($this->statusEqualsSubscribed($status)) {
			$status = 'subscribed';
		}

		return $status;
	}

	/**
	 * @param $status
	 * @return bool
	 */
	protected function statusEqualsUnsubscribed($status)
	{
		return $status == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED;
	}

	/**
	 * @param $status
	 * @return bool
	 */
	protected function statusEqualsSubscribed($status)
	{
		return $status == Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
	}

	/**
	 * @param $status
	 * @return bool
	 */
	protected function statusEqualsNotActive($status)
	{
		return $status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE;
	}

	/**
	 * @param $status
	 * @return bool
	 */
	protected function statusEqualsUnconfirmed($status)
	{
		return $status == Mage_Newsletter_Model_Subscriber::STATUS_UNCONFIRMED;
	}

	/**
	 * @param $subscriber
	 * @throws Mage_Core_Exception
	 */
	function deleteSubscriber($subscriber) {
		$helper = $this->getMailchimpHelper();
		$storeId = $subscriber->getStoreId();
		$listId = $helper->getGeneralList($storeId);
		try {
			$api = $helper->getApi($storeId);
			$emailHash = hash('md5', strtolower($subscriber->getSubscriberEmail()));
			$api->getLists()->getMembers()->update($listId, $emailHash, null, 'unsubscribed');
		}
		catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
			df_log($e);
		}
		catch (MailChimp_Error $e) {
			df_log($e);
			Mage::getSingleton('adminhtml/session')->addError(df_xts($e));
		}
		catch (Exception $e) {df_log($e);}
	}

	/**
	 * @param $emailAddress
	 */
	function update($emailAddress)
	{
		$subscriber = Mage::getSingleton('newsletter/subscriber')->loadByEmail($emailAddress);

		if ($subscriber->getId()) {
			$subscriber->setMailchimpSyncModified(1)
				->save();
		}
	}

	/**
	 * @param $errorMessage
	 */
	protected function addError($errorMessage)
	{
		Mage::getSingleton('core/session')->addError($errorMessage);
	}

	/**
	 * @param $mageMCHelper
	 */
	function setMailchimpHelper($mageMCHelper)
	{
		$this->_mcHelper = $mageMCHelper;
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function getMailchimpHelper()
	{
		return $this->_mcHelper;
	}

	/**
	 * @param $lastOrder
	 * @return array
	 */
	protected function getAddressFromLastOrder($lastOrder)
	{
		$addressData = array();

		if ($lastOrder && $lastOrder->getShippingAddress()) {
			$addressData = $lastOrder->getShippingAddress();
		}

		return $addressData;
	}

	/**
	 * @param $itemId
	 * @param $magentoStoreId
	 * @return Mage_Newsletter_Model_Subscriber \ subcriberSyncDataItem newsletter/subscriber if exists.
	 */
	protected function getSubscriberSyncDataItem($itemId, $magentoStoreId)
	{
		$subscriberSyncDataItem = null;
		$collection = Mage::getResourceModel('newsletter/subscriber_collection')
			->addFieldToFilter('subscriber_id', array('eq' => $itemId))
			->addFieldToFilter('store_id', array('eq' => $magentoStoreId))
			->setCurPage(1)
			->setPageSize(1);

		if ($collection->getSize()) {
			$subscriberSyncDataItem = $collection->getLastItem();
		}

		return $subscriberSyncDataItem;
	}

	/**
	 * @param $status
	 * @return bool
	 */
	protected function isSubscribed($status)
	{
		if ($status === Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED) {
			return true;
		}
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Subscribers_Collection
	 */
	function getResourceCollection()
	{
		/**
		 * @var $collection Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Subscribers_Collection
		 */
		$collection = Mage::getResourceModel('mailchimp/ecommercesyncdata_subscribers_collection');
		return $collection;
	}
}