<?php
use Mage_Customer_Model_Address as AddressC;
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as Sub;
use Mage_Sales_Model_Order as O;
use Mage_Sales_Model_Order_Address as AddressO;
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
	public $_d;

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
		return $i->_d;
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
		$d = $this->_d; /** @var array(string => string) $d */
		if (!dfa($d, 'FNAME')) {
			df_log('`FNAME` is missing in the merge fields', $this, [
				'Merge Fields' => $d
				,'Customer' => $this->customer()
				,'Subscriber' => $this->sub()
			]);
		}
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addStoreCodeFromCustomizedAttribute()
	 * @used-by self::addUnknownMergeField()
	 * @used-by self::addWebsiteId()
	 * @used-by self::buildCustomerAttributes()
	 * @used-by self::buildCustomizedAttributes()
 	 * @used-by self::processAttribute()
	 * @param $v
	 */
	private function set(string $k, $v):void {$this->_d[$k] = $v;}

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
		return $r->load($this->sub()->getCustomerId());
	});}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::buildCustomizedAttributes()
	 * @return mixed
	 */
	private function customizedAttributes(string $a, string $k) {
		$r = null;
		$addressGet = function($f) use($a, $k):void {/** @var string|Closure $f */
			if (
				($ad = $this->addressC($a, $this->customer())) /** @var AddressC $ad */
				&& 	($v = !is_string($f) ? $f($ad) : (df_starts_with($f, 'get') ? call_user_func([$ad, $f]) : $ad[$f]))
			) {
				$this->set($k, $v);
			}
		};
		switch ($a) {
			case 'billing_company':
			case 'shipping_company':
				$addressGet('company');
				break;
			case 'billing_telephone':
			case 'shipping_telephone':
				$addressGet('telephone');
				break;
			case 'billing_country':
			case 'shipping_country':
				$addressGet(function(AddressC $a):?string {return !($c = $a->getCountry()) ? null : df_country_ctn($c);});
				break;
			case 'billing_zipcode':
			case 'shipping_zipcode':
				$addressGet('postcode');
				break;
			case 'billing_state':
			case 'shipping_state':
				$addressGet('getRegion'); /** @uses Mage_Customer_Model_Address_Abstract::getRegion() */
				break;
			case 'dop':
				if ($v = $this->getLastDateOfPurchase()) {
					$this->set($k, $v);
				}
				break;
			case 'store_code':
				$this->set($k, Mage::getModel('core/store')->load($this->getStoreId())->getCode());
				break;
		}
		if (!df_nes($this->getMailChimpTagValue($k))) {
			$r = $this->getMailChimpTagValue($k);
		}
		return $r;
	}

	/**
	 * @param $attrSetId
	 * @param $customAtt
	 */
	private function buildCustomerAttributes($attrSetId, $customAtt, string $k):void {
		foreach ($attrSetId as $a) {
			if ($a['attribute_id'] == $customAtt) {
				$ac = $a['attribute_code'];
				if ('email' !== $ac) {
					$this->processAttribute($ac, $this->customer(), $k, $a);
				}
				$v = $this->getMailChimpTagValue($k);
				$this->dispatchMergeVarBefore($ac, $v);
				if (!is_null($v)) {
					$this->set($k, $v);
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
				'vars' => $this->_d,
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
	private function getAddressData($address) {
		$addressData = $this->addressO();
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
	 * 2024-05-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customizedAttributes()
	 */
	private function addressC(string $att, C $c):?AddressC {return
		!$this->addressO() ? null : $c->getPrimaryAddress('default_' . df_first(explode('_', $att)))
	;}

	/**
	 * 2024-05-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::getAddressData()
	 * @used-by self::addressC()
	 */
	private function addressO():?AddressO {return dfc($this, function() {return
		($o = $this->o()) ? df_ftn($o->getShippingAddress()) : null
	;});}

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
		$lastOrder = $this->o();
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
		$lastOrder = $this->o();
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
		$lastOrder = $this->o();
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
	 * @used-by self::buildCustomerAttributes()
	 * @used-by self::customizedAttributes()
	 */
	private function getMailChimpTagValue(string $k) {return dfa($this->_d, $k);}

	/**
	 * @return Varien_Object
	 */
	private function getNewVarienObject() {return new Varien_Object;}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::processAttribute()
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
			$this->_d = array_merge($this->_d, $mailchimpTags);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::getAddressData()
	 * @used-by self::addressC()
	 * @used-by self::getFirstName()
	 * @used-by self::getLastDateOfPurchase()
	 * @used-by self::getLastName()
	 */
	private function o():?O {return dfc($this, function() {/** @var OC $c */ return !count(
		$c = df_order_c()
			->addFieldToFilter('customer_email', ['eq' => $this->sub()->getSubscriberEmail()])
			->setOrder('created_at', 'DESC')
			->setPageSize(1)
	) ? null : $c->getLastItem();});}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::buildCustomerAttributes()
	 */
	private function processAttribute(string $a, C $c, $k, $attribute):void {
		switch ($a) {
			case 'default_billing':
			case 'default_shipping':
				if ($v = $this->getAddressData($c->getPrimaryAddress($a))) {
					$this->set($k, $v);
				}
				break;
			case 'gender':
				if ($v = $this->getCustomerGroupLabel($a, $c)) {
					$this->set($k, $this->getGenderLabel($this->_d, $k, $v));
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
				if (!is_null($v = $this->getUnknownMergeField($a, $c, $attribute))) {
					$this->set($k, $v);
				}
		}
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 * @used-by self::addFirstName()
	 * @used-by self::addLastName()
	 * @used-by self::dispatchEventMergeVarAfter()
	 * @used-by self::dispatchMergeVarBefore()
	 * @used-by self::o()
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