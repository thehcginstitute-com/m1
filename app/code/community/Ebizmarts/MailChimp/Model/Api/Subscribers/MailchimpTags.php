<?php
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as Sub;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Resource_Order_Collection as OC;
# 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
# https://github.com/cabinetsbay/site/issues/589
final class Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags {
	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addGender()
	 * @used-by self::add()
	 * @used-by self::_p()
	 * @used-by self::dispatchEventMergeVarAfter()
	 * @used-by self::getMailChimpTagValue()
	 * @used-by self::mergeMailchimpTags()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_getFName()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_getLName()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_setMailchimpTagsToCustomer()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::p()
	 * @var array
	 */
	public $_mailChimpTags;

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::makeMailchimpTagsBatchStructure()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_buildSubscriberData()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::updateSubscriber()
	 */
	static function p(Sub $sub, int $sid):array {
		$i = new self;
		$i->_sub = $sub;
		$i->_storeId = $sid;
		$i->_p();
		return $i->_mailChimpTags;
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::p()
	 */
	private function _p():void {
		$helper = hcg_mc_h();
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
		$d = $this->_mailChimpTags; /** @var array(string => string) $d */
		if (!dfa($d, 'FNAME')) {
			df_log('`FNAME` is missing in the merge fields', $this, [
				'Merge Fields' => $d
				,'Customer' => $this->customer()
				,'Subscriber' => $this->sub()
			]);
		}
	}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customerAttributes()
	 */
	private function _addTags(string $a, C $c, $k, $attribute):void {
		switch ($a) {
			case 'default_billing':
			case 'default_shipping':
				if ($v = $this->getAddressData($c->getPrimaryAddress($a))) {
					$this->set($k, $v);
				}
				break;
			case 'gender':
				if ($v = $this->getCustomerGroupLabel($a, $c)) {
					$this->set($k, $this->getGenderLabel($this->_mailChimpTags, $k, $v));
				}
				break;
			case 'group_id':
				$this->set($k, ($v = (int)$this->getCustomerGroupLabel($a, $c))
					? Mage::helper('customer')->getGroups()->toOptionHash()[$v]
					: 'NOT LOGGED IN'
				);
				break;
			case 'firstname':
				if ($v = $this->getFirstName($this->sub(), $c)) {
					$this->set($k, $v);
				}
				break;
			case 'lastname':
				if ($v = $this->getLastName($this->sub(), $c)) {
					$this->set($k, $v);
				}
				break;
			case 'store_id':
				$this->set($k, $this->getStoreId());
				break;
			case 'website_id':
				$this->set($k, $this->getWebSiteByStoreId($this->getStoreId()));
				break;
			case 'created_in':
				$this->set($k, Mage::getModel('core/store')->load($this->getStoreId())->getName());
				break;
			case 'dob':
				if ($this->getCustomerGroupLabel($a, $c)) {
					$this->set($k, $this->getDateOfBirth($a, $c));
				}
				break;
			default:
				$this->addUnknownMergeField($a, $k, $attribute, $c);
		}
	}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customizedAttributes()
	 */
	private function addCompany($customAtt, $customer, $key):void {
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$company = $address->getCompany();
			if ($company) {
				$this->set($key, $company);
			}
		}
	}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customizedAttributes()
	 */
	private function addCountryFromCustomizedAttribute($customAtt, $key, $customer):void {
		$address = $this->getAddressForCustomizedAttributes($customAtt, $customer);
		if ($address) {
			$countryCode = $address->getCountry();
			if ($countryCode) {
				$countryName = Mage::getModel('directory/country')->loadByCode($countryCode)->getName();
				$this->set($key, $countryName);
			}
		}
	}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customizedAttributes()
	 */
	private function addDopFromCustomizedAttribute($key):void {
		if ($dop = $this->getLastDateOfPurchase()) {
			$this->set($key, $dop);
		}
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addCompany()
	 * @used-by self::addCountryFromCustomizedAttribute()
	 * @used-by self::addDopFromCustomizedAttribute()
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
	private function set(string $k, $v):void {$this->_mailChimpTags[$k] = $v;}

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
				$this->set($key, $state);
			}
		}
	}

	/**
	 * @param $key
	 */
	private function addStoreCodeFromCustomizedAttribute($key):void
	{
		$storeCode = Mage::getModel('core/store')->load($this->getStoreId())->getCode();
		$this->set($key, $storeCode);
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
				$this->set($key, $telephone);
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
			$this->set($key, $mergeValue);
		}
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
				$this->set($key, $zipCode);
			}
		}
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 * @used-by self::customerAttributes()
	 * @used-by self::customizedAttributes()
	 * @used-by self::dispatchMergeVarBefore()
	 */
	private function customer():C {return dfc($this, function() {
		$r = Mage::getModel('customer/customer'); /** @var C $r */
		$r->setWebsiteId(df_store($this->getStoreId())->getWebsiteId());
		$r->load($this->sub()->getCustomerId());
		return $r;
	});}

	/**
	 * @param $attributeCode
	 * @param $key
	 * @param $attribute
	 * @return |null
	 */
	private function customerAttributes($attributeCode, $key, $attribute) {
		$customer = $this->customer();
		$eventValue = null;
		if ($attributeCode != 'email') {
			$this->_addTags($attributeCode, $customer, $key, $attribute);
		}
		if ($this->getMailChimpTagValue($key) !== null) {
			$eventValue = $this->getMailChimpTagValue($key);
		}
		return $eventValue;
	}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::buildCustomizedAttributes()
	 */
	private function customizedAttributes($customAtt, $key) {
		$eventValue = null;
		$customer = $this->customer();
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
					$this->set($key, $eventValue);
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
			$this->set($key, $eventValue);
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
				'subscriber' => $this->sub(),
				'vars' => $this->_mailChimpTags,
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
				'customer_id' => $this->customer()->getId(),
				'subscriber_email' => $this->sub()->getSubscriberEmail(),
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
		$lastOrder = $this->order();
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
		$lastOrder = $this->order();
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
		return hcg_mc_h_date()->formatDate(
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
		$lastOrder = $this->order();
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
		if ($genderValue == self::MALE) {
			$mergeVars[$key] = 'Male';
		} elseif ($genderValue == self::FEMALE) {
			$mergeVars[$key] = 'Female';
		}
		return $mergeVars[$key];
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
		$lastOrder = $this->order();
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
		$lastOrder = $this->order();
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
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customerAttributes()
	 * @used-by self::customizedAttributes()
	 */
	private function getMailChimpTagValue(string $k) {return dfa($this->_mailChimpTags, $k);}

	/**
	 * @return Varien_Object
	 */
	private function getNewVarienObject() {return new Varien_Object;}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_addTags()
	 * @used-by self::addCreatedIn()
	 * @used-by self::addStoreCodeFromCustomizedAttribute()
	 * @used-by self::addWebsiteId()
	 * @used-by self::_p()
	 * @used-by self::customer()
	 */
	private function getStoreId():int {return $this->_storeId;}

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
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::getAddressData()
	 * @used-by self::getAddressForCustomizedAttributes()
	 * @used-by self::getFirstName()
	 * @used-by self::getLastDateOfPurchase()
	 * @used-by self::getLastName()
	 */
	private function order():?O {return dfc($this, function() {/** @var OC $c */ return !count(
		$c = df_order_c()
			->addFieldToFilter('customer_email', ['eq' => $this->sub()->getSubscriberEmail()])
			->setOrder('created_at', 'DESC')
			->setPageSize(1)
	) ? null : $c->getLastItem();});}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 * @used-by self::addFirstName()
	 * @used-by self::addLastName()
	 * @used-by self::dispatchEventMergeVarAfter()
	 * @used-by self::dispatchMergeVarBefore()
	 * @used-by self::order()
	 * @used-by self::processMergeFields()
	 */
	private function sub():Sub {return $this->_sub;}

	/**
	 * @param $mapFields
	 * @return mixed
	 */
	private function unserializeMapFields($mapFields) {return hcg_mc_h()->unserialize($mapFields);}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::getGenderLabel()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::gender()
	 */
	const MALE = 1;

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::getGenderLabel()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::getGenderValue()
	 */
	const FEMALE = 2;

	/**
	 * @var int
	 */
	private $_storeId;

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::p()
	 * @used-by self::sub()
	 * @var Sub
	 */
	private $_sub;
}