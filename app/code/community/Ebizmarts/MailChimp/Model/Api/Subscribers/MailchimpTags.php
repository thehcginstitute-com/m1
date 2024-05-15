<?php
use Mage_Customer_Model_Address as AddressC;
use Mage_Customer_Model_Customer as C;
use Mage_Eav_Model_Entity_Attribute_Abstract as A;
use Mage_Eav_Model_Entity_Attribute_Interface as IA;
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
		# 2024-05-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# https://3v4l.org/akQm0#tabs
		foreach (hcg_mc_cfg_fields() as $f) {/** @var array(string => string) $f */
			if (($mg = dfa($f, 'magento')) && ($mc = dfa($f, 'mailchimp'))) { /** @var string $mg */ /** @var string $mc */
				$mc = strtoupper($mc);
				if (is_numeric($mg)) {
					$this->attCustomer(df_customer_att($mg), $mc);
				}
				else {
					$this->buildCustomizedAttributes($mg, $mc);
				}
			}
		}
		$newVars = $this->getNewVarienObject();
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
	 * 2024-05-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @param IA|A $a
	 * @used-by self::_p()
	 */
	private function attCustomer(IA $a, string $mc):void {
		$k = $a->getId(); /** @var int $k */
		switch ($ac = $a->getAttributeCode()) {/** @var string $ac */
			case 'default_billing':
			case 'default_shipping':
				if ($v = $this->getAddressData($c->getPrimaryAddress($ac))) {
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
			case 'lastname':
				$this->set($k, $this->name($ac));
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
				if ('email' !== $a->getAttributeCode() && !is_null($v = $this->getUnknownMergeField($a, $c, $attribute))) {
					$this->set($k, $v);
				}
		}
		if (!is_null($v = $this->getMailChimpTagValue($mc))) {
			$this->set($mc, $v);
		}
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 * @used-by self::customerAttributes()
	 * @used-by self::customizedAttributes()
	 * @used-by self::name()
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
	private function customizedAttributes(string $mg, string $mc) {
		$r = null;
		$addressGet = function($f) use($mg, $mc):void {/** @var string|Closure $f */
			if (
				($ad = !$this->addressO() ? null : $this->customer()->getPrimaryAddress('default_' . df_first(explode('_', $mg))))
				/** @var AddressC $ad */
				&& 	($v = !is_string($f) ? $f($ad) : (df_starts_with($f, 'get') ? call_user_func([$ad, $f]) : $ad[$f]))
				/** @var mixed $v */
			) {
				$this->set($mc, $v);
			}
		};
		switch ($mg) {
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
					$this->set($mc, $v);
				}
				break;
			case 'store_code':
				$this->set($mc, Mage::getModel('core/store')->load($this->getStoreId())->getCode());
				break;
		}
		if (!df_nes($this->getMailChimpTagValue($mc))) {
			$r = $this->getMailChimpTagValue($mc);
		}
		return $r;
	}

	/**
	 * 2024-05-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 */
	private function buildCustomizedAttributes(string $mg, string $mc):void {
		if (!is_null($v = $this->customizedAttributes($mg, $mc))) {
			$this->set($mc, $v);
		}
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
	 * @used-by self::getAddressData()
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
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::attCustomer()
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
	 * @used-by self::attCustomer()
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
	 * 2024-05-15 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::attCustomer()
	 */
	private function name(string $k):string {return $this->customer()[$k] ?: ($this->sub()["subscriber_$k"] ?: (
		($o = $this->o()) ? $o["customer_$k"] : df_error("Unable to find out `{$k}` for the customer.")
	));}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addressC()
	 * @used-by self::getAddressData()
	 * @used-by self::getLastDateOfPurchase()
	 * @used-by self::name()
	 */
	private function o():?O {return dfc($this, function() {/** @var OC $c */ return !count(
		$c = df_order_c()
			->addFieldToFilter('customer_email', ['eq' => $this->sub()->getSubscriberEmail()])
			->setOrder('created_at', 'DESC')
			->setPageSize(1)
	) ? null : $c->getLastItem();});}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addStoreCodeFromCustomizedAttribute()
	 * @used-by self::addUnknownMergeField()
	 * @used-by self::addWebsiteId()
	 * @used-by self::attCustomer()
	 * @used-by self::buildCustomizedAttributes()
	 * @param int|string $k
	 * @param $v
	 */
	private function set($k, $v):void {$this->_d[$k] = $v;}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 * @used-by self::name()
	 * @used-by self::o()
	 * @used-by self::processMergeFields()
	 */
	private function sub():Sub {return $this->_sub;}

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