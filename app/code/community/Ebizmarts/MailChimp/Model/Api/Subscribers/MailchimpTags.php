<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as Sub;
use Mage_Sales_Model_Order as O;
# 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
# https://github.com/cabinetsbay/site/issues/589
final class Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags {
	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::_buildMailchimpTags()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_buildMailchimpTags()
	 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::p()
	 */
	function __construct() {
		$this->setMailChimpHelper();
		$this->setMailChimpDateHelper();
		$this->setMailChimpWebhookHelper();
		$this->_interestGroupHandle = Mage::getModel('mailchimp/api_subscribers_InterestGroupHandle');
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::_buildMailchimpTags()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_buildMailchimpTags()
	 * @throws Mage_Core_Exception
	 */
	function buildMailChimpTags():void {
		$helper = $this->getMailchimpHelper();
		$storeId = $this->getStoreId();
		$mapFields = $helper->getMapFields($storeId);
		$maps = $this->unserializeMapFields($mapFields);
		$attrSetId = $this->getEntityAttributeCollection()
			->setEntityTypeFilter(1)
			->addSetInfo()
			->getData();
		foreach ($maps as $map) {
			$customAtt = $map['magento'];
			$chimpTag = $map['mailchimp'];
			if ($chimpTag && $customAtt) {
				$key = strtoupper($chimpTag);
				if (is_numeric($customAtt)) {
					$this->buildCustomerAttributes($attrSetId, $customAtt, $key);
				} else {
					$this->buildCustomizedAttributes($customAtt, $key);
				}
			}
		}
		$newVars = $this->getNewVarienObject();
		$this->dispatchEventMergeVarAfter($newVars);

		if ($newVars->hasData()) {
			$this->mergeMailchimpTags($newVars->getData());
		}
		# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "`Ebizmarts_MailChimp`: «Your merge fields were invalid» /
		# «field [FNAME] : Please enter a value» /
		# «field [LNAME] : Please enter a value»": https://github.com/thehcginstitute-com/m1/issues/507
		$d = $this->getMailChimpTags(); /** @var array(string => string) $d */
		if (!dfa($d, 'FNAME')) {
			df_log('`FNAME` is missing in the merge fields', $this, [
				'Merge Fields' => $d
				,'Customer' => $this->getCustomer()
				,'Subscriber' => $this->getSubscriber()
			]);
		}
	}

	function getLastOrder():O {return $this->_lastOrder;}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Date
	 */
	function getMailchimpDateHelper() {return $this->_mcDateHelper;}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	function getMailchimpHelper()
	{
		return $this->_mcHelper;
	}

	function getMailChimpTags():array {return $this->_mailChimpTags;}

	/**
	 * @return mixed|null
	 */
	function getMailChimpTagValue(string $key)
	{
		$mailchimpTagValue = null;

		if (isset($this->_mailChimpTags[$key])) {
			$mailchimpTagValue = $this->_mailChimpTags[$key];
		}

		return $mailchimpTagValue;
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Webhook
	 */
	function getMailchimpWebhookHelper() {return $this->_mcWebhookHelper;}

	/**
	 * @return int
	 */
	function getStoreId() {return $this->_storeId;}

	function getSubscriber():Sub {return $this->_subscriber;}

	/**
	 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::p()
	 * @param $data
	 * @param bool $subscribe
	 * @throws Mage_Core_Exception
	 */
	function processMergeFields($data, $subscribe = false):void
	{
		$helper = $this->getMailchimpHelper();
		$email = $data['email'];
		$listId = $data['list_id'];

		$STATUS_SUBSCRIBED = Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
		$storeId = $helper->getMagentoStoreIdsByListId($listId)[0];

		$this->_mailChimpTags = $helper->getMapFields($storeId);
		$this->_mailChimpTags = $this->unserializeMapFields($this->_mailChimpTags);

		$customer = $helper->loadListCustomer($listId, $email);

		if ($customer) {
			$this->setCustomer($customer);
			$this->_setMailchimpTagsToCustomer($data);
		}

		$subscriber = $helper->loadListSubscriber($listId, $email);
		$fname = $this->_getFName($data);
		$lname = $this->_getLName($data);

		if ($subscriber->getId()) {
			if ($subscriber->getStatus() != $STATUS_SUBSCRIBED && $subscribe) {
				$subscriber->setStatus($STATUS_SUBSCRIBED);
				$subscriber->setSubscriberFirstname($fname);
				$subscriber->setSubscriberLastname($lname);
			}
		} else {
			if ($subscribe) {
				$helper->subscribeMember($subscriber);
			} else {
				/**
				 * Mailchimp subscriber not currently in magento newsletter subscribers.
				 * Get mailchimp subscriber status and add missing newsletter subscriber.
				 */
				$this->_addSubscriberData($subscriber, $fname, $lname, $email, $listId);
			}
		}

		$subscriber->save();
		$this->setSubscriber($subscriber);

		if (isset($data['merges']['GROUPINGS'])) {
			$interestGroupHandle = $this->_getInterestGroupHandleModel();

			if ($this->getSubscriber() === null) {
				$interestGroupHandle->setCustomer($this->getCustomer());
			} else {
				$interestGroupHandle->setSubscriber($this->getSubscriber());
			}

			$interestGroupHandle->setGroupings($data['merges']['GROUPINGS'])
				->setListId($listId)
				->processGroupsData();
		}
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::_buildMailchimpTags()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_buildMailchimpTags()
	 */
	function setCustomer(C $v):void {$this->_customer = $v;}

	function setLastOrder(O $lastOrder):void {$this->_lastOrder = $lastOrder;}

	/**
	 * @param $storeId
	 */
	function setStoreId($storeId):void {$this->_storeId = $storeId;}

	function setSubscriber(Sub $subscriber):void {$this->_subscriber = $subscriber;}

	/**
	 * @param $subscriber
	 * @param $fname
	 * @param $lname
	 * @param $email
	 * @param $listId
	 * @throws Exception
	 */
	private function _addSubscriberData($subscriber, $fname, $lname, $email, $listId):void
	{
		$helper = $this->getMailchimpHelper();
		$webhookHelper = $this->getMailchimpWebhookHelper();
		$scopeArray = $helper->getFirstScopeFromConfig(
			Ebizmarts_MailChimp_Model_Config::GENERAL_LIST,
			$listId
		);
		$api = $helper->getApi($scopeArray['scope_id'], $scopeArray['scope']);

		try {
			$subscriber->setSubscriberFirstname($fname);
			$subscriber->setSubscriberLastname($lname);
			$md5HashEmail = hash('md5', strtolower($email));
			$member = $api->getLists()->getMembers()->get(
				$listId,
				$md5HashEmail,
				null,
				null
			);

			if ($member['status'] == 'subscribed') {
				$helper->subscribeMember($subscriber);
			} else if ($member['status'] == 'unsubscribed') {
				if (!$webhookHelper->getWebhookDeleteAction($subscriber->getStoreId())) {
					$helper->unsubscribeMember($subscriber);
				}
			}
		} catch (MailChimp_Error $e) {
			$helper->logError($e->getFriendlyMessage());
		} catch (Exception $e) {
			$helper->logError($e->getMessage());
		}
	}

	/**
	 * @param $attributeCode
	 * @param $subscriber
	 * @param $customer
	 * @param $key
	 * @param $attribute
	 */
	private function _addTags($attributeCode, $subscriber, $customer, $key, $attribute):void
	{
		if ($attributeCode == 'default_billing' || $attributeCode == 'default_shipping') {
			$this->addDefaultShipping($attributeCode, $key, $customer);
		} elseif ($attributeCode == 'gender') {
			$this->addGender($attributeCode, $key, $customer);
		} elseif ($attributeCode == 'group_id') {
			$this->addGroupId($attributeCode, $key, $customer);
		} elseif ($attributeCode == 'firstname') {
			$this->addFirstName($key, $subscriber, $customer);
		} elseif ($attributeCode == 'lastname') {
			$this->addLastName($key, $subscriber, $customer);
		} elseif ($attributeCode == 'store_id') {
			$this->addMailChimpTag($key, $this->getStoreId());
		} elseif ($attributeCode == 'website_id') {
			$this->addWebsiteId($key);
		} elseif ($attributeCode == 'created_in') {
			$this->addCreatedIn($key);
		} elseif ($attributeCode == 'dob') {
			$this->addDob($attributeCode, $key, $customer);
		} else {
			$this->addUnknownMergeField($attributeCode, $key, $attribute, $customer);
		}
	}

	/**
	 * @param $attrId
	 * @return string
	 */
	private function _getAttrbuteCode($attrId) {
		$attributeCode = Mage::getModel('eav/entity_attribute')->load($attrId)->getAttributeCode();
		return $attributeCode;
	}

	/**
	 * @param $attrCode
	 * @return int
	 */
	private function _getAttrbuteId($attrCode) {
		$attribute = Mage::getModel('eav/entity_attribute')
			->getCollection()
			->addFieldToFilter('attribute_code', $attrCode)
			->getFirstItem();
		$attrId = $attribute->getId();
		return $attrId;
	}

	/**
	 * @param $data
	 * @return string
	 */
	private function _getFName($data) {
		$attrId = $this->_getAttrbuteId('firstname');
		$magentoTag = '';
		foreach ($this->_mailChimpTags as $tag) {
			if ($tag['magento'] == $attrId) {
				$magentoTag = $tag['mailchimp'];
				break;
			}
		}
		return $data['merges'][$magentoTag];
	}

	/**
	 * @return false|Mage_Core_Model_Abstract
	 */
	private function _getInterestGroupHandleModel() {return $this->_interestGroupHandle;}

	/**
	 * @param $data
	 * @return string
	 */
	private function _getLName($data) {
		$attrId = $this->_getAttrbuteId('lastname');
		$magentoTag = '';
		foreach ($this->_mailChimpTags as $tag) {
			if ($tag['magento'] == $attrId) {
				$magentoTag = $tag['mailchimp'];
				break;
			}
		}
		return $data['merges'][$magentoTag];
	}

	/**
	 * @param $attrId
	 * @return bool
	 */
	private function _isAddress($attrId) {
		if (is_numeric($attrId)) {
			// Gets the magento attr_code.
			$attributeCode = $this->_getAttrbuteCode($attrId);
			if ($attributeCode == 'default_billing' || $attributeCode == 'default_shipping') {
				return true;
			}
		}
		return false;
	}

	/**
	 * Sets the mailchimp tag value for tue customer.
	 * @param $key
	 * @param $value
	 * @param $mapFields
	 * @param $customer
	 */
	private function _setMailchimpTagToCustomer($key, $value, $mapFields, $customer):void
	{
		$ignore = array(
			'billing_company', 'billing_country', 'billing_zipcode', 'billing_state', 'billing_telephone',
			'shipping_company', 'shipping_telephone', 'shipping_country', 'shipping_zipcode', 'shipping_state',
			'dop', 'store_code');

		foreach ($mapFields as $map) {
			if ($map['mailchimp'] == $key) {
				if (!in_array($map['magento'], $ignore) && !$this->_isAddress($map['magento'])) {
					if ($key != 'GENDER') {
						$customer->setData($map['magento'], $value);
					} else {
						$customer->setData('gender', $this->getGenderValue($value));
					}
				}
			}
		}
	}

	/**
	 * Iterates the mailchimp tags.
	 * @param $data
	 * @param $listId
	 * @throws Mage_Core_Exception
	 */
	private function _setMailchimpTagsToCustomer($data):void {
		$customer = $this->getCustomer();
		foreach ($data['merges'] as $key => $value) {
			if (!empty($value)) {
				if (is_array($this->_mailChimpTags)) {
					if ($key !== 'GROUPINGS') {
						$this->_setMailchimpTagToCustomer($key, $value, $this->_mailChimpTags, $customer);
					}
				}
			}
		}
		$customer->save();
	}

	/**
	 * @param $customAtt
	 * @param $customer
	 * @param $mergeVars
	 * @param $key
	 * @return mixed
	 */
	private function addCompany($customAtt, $customer, $key):void {
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$company = $address->getCompany();
			if ($company) {
				$this->addMailChimpTag($key, $company);
			}
		}
	}

	/**
	 * @param $customAtt
	 * @param $key
	 * @param $customer
	 */
	private function addCountryFromCustomizedAttribute($customAtt, $key, $customer):void
	{
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$countryCode = $address->getCountry();
			if ($countryCode) {
				$countryName = Mage::getModel('directory/country')->loadByCode($countryCode)->getName();
				$this->addMailChimpTag($key, $countryName);
			}
		}
	}

	/**
	 * @param $key
	 */
	private function addCreatedIn($key):void
	{
		$storeName = Mage::getModel('core/store')->load($this->getStoreId())->getName();
		$this->addMailChimpTag($key, $storeName);
	}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $customer
	 */
	private function addDefaultShipping($attributeCode, $key, $customer):void
	{
		$address = $customer->getPrimaryAddress($attributeCode);
		$addressData = $this->getAddressData($address);

		if (!empty($addressData)) {
			$this->addMailChimpTag($key, $addressData);
		}
	}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $customer
	 */
	private function addDob($attributeCode, $key, $customer):void
	{
		if ($this->getCustomerGroupLabel($attributeCode, $customer)) {
			$this->addMailChimpTag($key, $this->getDateOfBirth($attributeCode, $customer));
		}
	}

	/**
	 * @param $key
	 * @param $subscriberEmail
	 */
	private function addDopFromCustomizedAttribute($key):void
	{
		$dop = $this->getLastDateOfPurchase();
		if ($dop) {
			$this->addMailChimpTag($key, $dop);
		}
	}

	/**
	 * @param $key
	 * @param $subscriber
	 * @param $customer
	 */
	private function addFirstName($key, $subscriber, $customer):void
	{
		$firstName = $this->getFirstName($subscriber, $customer);

		if ($firstName) {
			$this->addMailChimpTag($key, $firstName);
		}
	}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $customer
	 */
	private function addGender($attributeCode, $key, $customer):void
	{
		if ($this->getCustomerGroupLabel($attributeCode, $customer)) {
			$genderValue = $this->getCustomerGroupLabel($attributeCode, $customer);
			$this->addMailChimpTag($key, $this->getGenderLabel($this->_mailChimpTags, $key, $genderValue));
		}
	}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $customer
	 */
	private function addGroupId($attributeCode, $key, $customer):void
	{
		if ($this->getCustomerGroupLabel($attributeCode, $customer)) {
			$groupId = (int)$this->getCustomerGroupLabel($attributeCode, $customer);
			$customerGroup = Mage::helper('customer')->getGroups()->toOptionHash();
			$this->addMailChimpTag($key, $customerGroup[$groupId]);
		} else {
			$this->addMailChimpTag($key, 'NOT LOGGED IN');
		}
	}

	/**
	 * @param $key
	 * @param $subscriber
	 * @param $customer
	 */
	private function addLastName($key, $subscriber, $customer):void
	{
		$lastName = $this->getLastName($subscriber, $customer);

		if ($lastName) {
			$this->addMailChimpTag($key, $lastName);
		}
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addCompany()
	 * @used-by self::addCountryFromCustomizedAttribute()
	 * @used-by self::addCreatedIn()
	 * @used-by self::addDefaultShipping()
	 * @used-by self::addDob()
	 * @used-by self::addDopFromCustomizedAttribute()
	 * @used-by self::addFirstName()
	 * @used-by self::addGender()
	 * @used-by self::addGroupId()
	 * @used-by self::addLastName()
	 * @used-by self::addStateFromCustomizedAttribute()
	 * @used-by self::addStoreCodeFromCustomizedAttribute()
	 * @used-by self::addTelephoneFromCustomizedAttribute()
	 * @used-by self::addUnknownMergeField()
	 * @used-by self::addWebsiteId()
	 * @used-by self::addZipCodeFromCustomizedAttribute()
	 * @used-by self::buildCustomerAttributes()
	 * @used-by self::buildCustomizedAttributes()
 	 * @used-by self::_addTags()
	 * @param $v
	 */
	private function addMailChimpTag(string $k, $v):void {$this->_mailChimpTags[$k] = $v;}

	/**
	 * @param $customAtt
	 * @param $key
	 * @param $customer
	 */
	private function addStateFromCustomizedAttribute($customAtt, $key, $customer):void
	{
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$state = $address->getRegion();
			if ($state) {
				$this->addMailChimpTag($key, $state);
			}
		}
	}

	/**
	 * @param $key
	 */
	private function addStoreCodeFromCustomizedAttribute($key):void
	{
		$storeCode = Mage::getModel('core/store')->load($this->getStoreId())->getCode();
		$this->addMailChimpTag($key, $storeCode);
	}

	/**
	 * @param $customAtt
	 * @param $key
	 * @param $customer
	 */
	private function addTelephoneFromCustomizedAttribute($customAtt, $key, $customer):void
	{
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$telephone = $address->getTelephone();
			if ($telephone) {
				$this->addMailChimpTag($key, $telephone);
			}
		}
	}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $attribute
	 * @param $customer
	 */
	private function addUnknownMergeField($attributeCode, $key, $attribute, $customer):void
	{
		$mergeValue = $this->getUnknownMergeField($attributeCode, $customer, $attribute);
		if ($mergeValue !== null) {
			$this->addMailChimpTag($key, $mergeValue);
		}
	}

	/**
	 * @param $key
	 */
	private function addWebsiteId($key):void
	{
		$websiteId = $this->getWebSiteByStoreId($this->getStoreId());
		$this->addMailChimpTag($key, $websiteId);
	}

	/**
	 * @param $customAtt
	 * @param $key
	 * @param $customer
	 */
	private function addZipCodeFromCustomizedAttribute($customAtt, $key, $customer):void
	{
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$zipCode = $address->getPostcode();
			if ($zipCode) {
				$this->addMailChimpTag($key, $zipCode);
			}
		}
	}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $attribute
	 * @return |null
	 */
	private function customerAttributes($attributeCode, $key, $attribute)
	{
		$subscriber = $this->getSubscriber();
		$customer = $this->getCustomer();

		$eventValue = null;

		if ($attributeCode != 'email') {
			$this->_addTags($attributeCode, $subscriber, $customer, $key, $attribute);
		}

		if ($this->getMailChimpTagValue($key) !== null) {
			$eventValue = $this->getMailChimpTagValue($key);
		}

		return $eventValue;
	}

	/**
	 * @param $customAtt
	 * @param $key
	 * @return mixed | null
	 */
	private function customizedAttributes($customAtt, $key)
	{
		$eventValue = null;
		$customer = $this->getCustomer();

		if ($customAtt == 'billing_company' || $customAtt == 'shipping_company') {
			$this->addCompany($customAtt, $customer, $key);
		} elseif ($customAtt == 'billing_telephone' || $customAtt == 'shipping_telephone') {
			$this->addTelephoneFromCustomizedAttribute($customAtt, $key, $customer);
		} elseif ($customAtt == 'billing_country' || $customAtt == 'shipping_country') {
			$this->addCountryFromCustomizedAttribute($customAtt, $key, $customer);
		} elseif ($customAtt == 'billing_zipcode' || $customAtt == 'shipping_zipcode') {
			$this->addZipCodeFromCustomizedAttribute($customAtt, $key, $customer);
		} elseif ($customAtt == 'billing_state' || $customAtt == 'shipping_state') {
			$this->addStateFromCustomizedAttribute($customAtt, $key, $customer);
		} elseif ($customAtt == 'dop') {
			$this->addDopFromCustomizedAttribute($key);
		} elseif ($customAtt == 'store_code') {
			$this->addStoreCodeFromCustomizedAttribute($key);
		}

		if ((string)$this->getMailChimpTagValue($key) != '') {
			$eventValue = $this->getMailChimpTagValue($key);
		}

		return $eventValue;
	}

	/**
	 * @param $attrSetId
	 * @param $customAtt
	 * @param $key
	 */
	private function buildCustomerAttributes($attrSetId, $customAtt, $key):void {
		$eventValue = null;
		foreach ($attrSetId as $attribute) {
			if ($attribute['attribute_id'] == $customAtt) {
				$attributeCode = $attribute['attribute_code'];
				$eventValue = $this->customerAttributes(
					$attributeCode, $key, $attribute
				);

				$this->dispatchMergeVarBefore($attributeCode, $eventValue);
				if ($eventValue !== null) {
					$this->addMailChimpTag($key, $eventValue);
				}
			}
		}
	}

	/**
	 * @param $customAtt
	 * @param $key
	 */
	private function buildCustomizedAttributes($customAtt, $key):void
	{
		$eventValue = null;
		$eventValue = $this->customizedAttributes(
			$customAtt, $key
		);

		$this->dispatchMergeVarBefore($customAtt, $eventValue);
		if ($eventValue !== null) {
			$this->addMailChimpTag($key, $eventValue);
		}
	}

	/**
	 * Allow possibility to add new vars in 'new_vars' array
	 *
	 * @param $newVars
	 */
	private function dispatchEventMergeVarAfter(&$newVars):void
	{
		Mage::dispatchEvent(
			'mailchimp_merge_field_send_after',
			array(
				'subscriber' => $this->getSubscriber(),
				'vars' => $this->getMailChimpTags(),
				'new_vars' => &$newVars
			)
		);
	}

	/**
	 * Add possibility to change value on certain merge tag
	 *
	 * @param $attributeCode
	 * @param $eventValue
	 */
	private function dispatchMergeVarBefore($attributeCode, &$eventValue):void
	{
		Mage::dispatchEvent(
			'mailchimp_merge_field_send_before',
			array(
				'customer_id' => $this->getCustomer()->getId(),
				'subscriber_email' => $this->getSubscriber()->getSubscriberEmail(),
				'merge_field_tag' => $attributeCode,
				'merge_field_value' => &$eventValue
			)
		);
	}

	/**
	 * @param $address
	 * @return array
	 */
	private function getAddressData($address)
	{
		$lastOrder = $this->getLastOrderByEmail();
		$addressData = $this->getAddressFromLastOrder($lastOrder);
		if (!empty($addressData)) {
			if ($address) {
				$street = $address->getStreet();
				if (count($street) > 1) {
					$addressData["addr1"] = $street[0];
					$addressData["addr2"] = $street[1];
				} else {
					if (!empty($street[0])) {
						$addressData["addr1"] = $street[0];
					}
				}

				if ($address->getCity()) {
					$addressData["city"] = $address->getCity();
				}

				if ($address->getRegion()) {
					$addressData["state"] = $address->getRegion();
				}

				if ($address->getPostcode()) {
					$addressData["zip"] = $address->getPostcode();
				}

				if ($address->getCountry()) {
					$addressData["country"] = Mage::getModel('directory/country')
						->loadByCode($address->getCountry())
						->getName();
				}
			}
		}

		return $addressData;
	}

	/**
	 * @param $customAtt
	 * @param $customer
	 * @return array
	 */
	private function getAddressForCustomizedAttributes($customAtt, $customer)
	{
		$lastOrder = $this->getLastOrderByEmail();
		$address = $this->getAddressFromLastOrder($lastOrder);
		if (!empty($address)) {
			$addr = explode('_', $customAtt);
			$address = $customer->getPrimaryAddress('default_' . $addr[0]);
		}

		return $address;
	}

	/**
	 * @param $lastOrder
	 * @return array
	 */
	private function getAddressFromLastOrder($lastOrder) {
		$addressData = array();
		if ($lastOrder && $lastOrder->getShippingAddress()) {
			$addressData = $lastOrder->getShippingAddress();
		}
		return $addressData;
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_setMailchimpTagsToCustomer()
	 * @used-by self::buildMailChimpTags()
	 * @used-by self::customerAttributes()
	 * @used-by self::customizedAttributes()
	 * @used-by self::dispatchMergeVarBefore()
	 * @used-by self::processMergeFields()
	 */
	private function getCustomer():C {return $this->_customer;}

	/**
	 * @param $attributeCode
	 * @param $customer
	 * @return string
	 */
	private function getCustomerGroupLabel($attributeCode, $customer) {return $customer->getData($attributeCode);}

	/**
	 * @param $attributeCode
	 * @param $customer
	 * @return mixed
	 * @throws Mage_Core_Model_Store_Exception
	 */
	private function getDateOfBirth($attributeCode, $customer)
	{
		return $this->getMailchimpDateHelper()->formatDate(
			$this->getCustomerGroupLabel($attributeCode, $customer),
			'm/d', 1
		);
	}

	/**
	 * @return Object
	 */
	private function getEntityAttributeCollection() {return Mage::getResourceModel('eav/entity_attribute_collection');}

	/**
	 * @param $subscriber
	 * @param $customer
	 * @return string
	 */
	private function getFirstName($subscriber, $customer) {
		$lastOrder = $this->getLastOrderByEmail();
		$firstName = $customer->getFirstname();
		if (!$firstName) {
			if ($subscriber->getSubscriberFirstname()) {
				$firstName = $subscriber->getSubscriberFirstname();
			} elseif ($lastOrder && $lastOrder->getCustomerFirstname()) {
				$firstName = $lastOrder->getCustomerFirstname();
			}
		}
		return $firstName;
	}

	/**
	 * @param $mergeVars
	 * @param $key
	 * @param $genderValue
	 * @return string
	 */
	private function getGenderLabel($mergeVars, $key, $genderValue) {
		if ($genderValue == self::GENDER_VALUE_MALE) {
			$mergeVars[$key] = 'Male';
		} elseif ($genderValue == self::GENDER_VALUE_FEMALE) {
			$mergeVars[$key] = 'Female';
		}
		return $mergeVars[$key];
	}

	/**
	 * @param $genderLabel
	 * @return int
	 */
	private function getGenderValue($genderLabel) {
		$genderValue = 0;
		if ($genderLabel == 'Male') {
			$genderValue = self::GENDER_VALUE_MALE;
		} elseif ($genderLabel == 'Female') {
			$genderValue = self::GENDER_VALUE_FEMALE;
		}
		return $genderValue;
	}

	/**
	 * If orders with the given email exists, returns the date of the last order made.
	 *
	 * @param  $subscriberEmail
	 * @return null
	 */
	private function getLastDateOfPurchase()
	{
		$lastDateOfPurchase = null;
		$lastOrder = $this->getLastOrderByEmail();
		if ($lastOrder !== null) {
			$lastDateOfPurchase = $lastOrder->getCreatedAt();
		}

		return $lastDateOfPurchase;
	}

	/**
	 * @param $subscriber
	 * @param $customer
	 * @return string
	 */
	private function getLastName($subscriber, $customer) {
		$lastOrder = $this->getLastOrderByEmail();
		$lastName = $customer->getLastname();
		if (!$lastName) {
			if ($subscriber->getSubscriberLastname()) {
				$lastName = $subscriber->getSubscriberLastname();
			} elseif ($lastOrder && $lastOrder->getCustomerLastname()) {
				$lastName = $lastOrder->getCustomerLastname();
			}
		}
		return $lastName;
	}

	/**
	 * return the latest order for this subscriber
	 * @return Mage_Sales_Model_Order
	 */
	private function getLastOrderByEmail() {
		$lastOrder = $this->getLastOrder();
		if ($lastOrder === null) {
			$helper = $this->getMailchimpHelper();
			$orderCollection = $helper->getOrderCollectionByCustomerEmail($this->getSubscriber()->getSubscriberEmail())
				->setOrder('created_at', 'DESC')
				->setPageSize(1);
			if ($this->isNotEmptyOrderCollection($orderCollection)) {
				$lastOrder = $orderCollection->getLastItem();
				$this->setLastOrder($lastOrder);
			}
		}
		return $lastOrder;
	}

	/**
	 * @return Varien_Object
	 */
	private function getNewVarienObject() {return new Varien_Object;}

	/**
	 * @param $attributeCode
	 * @param $customer
	 * @param $attribute
	 * @return mixed
	 */
	private function getUnknownMergeField($attributeCode, $customer, $attribute)
	{
		$optionValue = null;

		$attrValue = $this->getCustomerGroupLabel($attributeCode, $customer);
		if ($attrValue !== null) {
			if ($attribute['frontend_input'] == 'select' && $attrValue) {
				$attr = $customer->getResource()->getAttribute($attributeCode);
				$optionValue = $attr->getSource()->getOptionText($attrValue);
			} elseif ($attrValue) {
				$optionValue = $attrValue;
			}
		}

		return $optionValue;
	}

	/**
	 * @param $storeId
	 * @return mixed
	 */
	private function getWebSiteByStoreId($storeId) {return Mage::getModel('core/store')->load($storeId)->getWebsiteId();}

	/**
	 * @param $orderCollection
	 * @return bool
	 */
	private function isNotEmptyOrderCollection($orderCollection) {return $orderCollection->getSize() > 0;}

	/**
	 * @param $mailchimpTags
	 * @return bool
	 */
	private function mergeMailchimpTags($mailchimpTags) {
		if (is_array($mailchimpTags)) {
			$this->_mailChimpTags = array_merge($this->_mailChimpTags, $mailchimpTags);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param $mageMCDateHelper
	 */
	private function setMailChimpDateHelper():void {$this->_mcDateHelper = Mage::helper('mailchimp/date');}

	/**
	 * @param $mageMCHelper
	 */
	private function setMailChimpHelper():void {$this->_mcHelper = hcg_mc_h();}

	/**
	 * @param $mageMCWebhookHelper
	 */
	private function setMailChimpWebhookHelper():void {$this->_mcWebhookHelper = Mage::helper('mailchimp/webhook');}

	/**
	 * @param $mapFields
	 * @return mixed
	 */
	private function unserializeMapFields($mapFields) {return $this->_mcHelper->unserialize($mapFields);}
	
	const GENDER_VALUE_MALE = 1;
	const GENDER_VALUE_FEMALE = 2;

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::getCustomer()
	 * @used-by self::setCustomer()
	 * @var C
	 */
	private $_customer;

	/**
	 * @var int
	 */
	private $_storeId;
	/**
	 * @var array
	 */
	private $_mailChimpTags;
	/**
	 * @var Mage_Newsletter_Model_Subscriber
	 */
	private $_subscriber;

	/**
	 * @var Ebizmarts_MailChimp_Helper_Data
	 */
	private $_mcHelper;
	/**
	 * @var Ebizmarts_MailChimp_Helper_Date
	 */
	private $_mcDateHelper;
	/**
	 * @var Ebizmarts_MailChimp_Helper_Webhook
	 */
	private $_mcWebhookHelper;
	/**
	 * @var Mage_Sales_Model_Order
	 */
	private $_lastOrder;

	/**
	 * @var Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle
	 */
	private $_interestGroupHandle;	
}