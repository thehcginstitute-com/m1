<?php
use Mage_Customer_Model_Address as AddressC;
use Mage_Customer_Model_Address_Abstract as AddressA;
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
				,'Customer' => $this->c()
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
				$this->set($k, $this->vAddress($ac));
				break;
			case 'gender':
				if ($v = $this->getCustomerGroupLabel($a, $this->c())) {
					$this->set($k, $this->getGenderLabel($this->_d, $k, $v));
				}
				break;
			case 'group_id':
				$this->set($k, ($v = (int)$this->getCustomerGroupLabel($a, $this->c()))
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
				if ($this->getCustomerGroupLabel($a, $this->c())) {
					$this->set($k, $this->getDateOfBirth($a, $this->c()));
				}
				break;
			default:
				if ('email' !== $a->getAttributeCode() && !is_null($v = $this->getUnknownMergeField($a, $this->c(), $attribute))) {
					$this->set($k, $v);
				}
		}
		$this->set($mc, $this->getMailChimpTagValue($mc));
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`":
	 * https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_p()
	 * @used-by self::address()
	 * @used-by self::customerAttributes()
	 * @used-by self::customizedAttributes()
	 * @used-by self::name()
	 */
	private function c():C {return dfc($this, function() {return df_customer($this->sub());});}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::buildCustomizedAttributes()
	 * @return mixed
	 */
	private function customizedAttributes(string $mg, string $mc) {
		$r = null;
		$addressGet = function($f) use($mg, $mc):void {/** @var string|Closure $f */
			if ($v = $this->addressGet($mg, $f)) {
				/** @var mixed $v */
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
	 * 2024-05-15 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * 2) https://mailchimp.com/developer/marketing/docs/merge-fields#add-merge-data-to-contacts
	 * 3) "`Ebizmarts_MailChimp`: «merge_fields.BILLING : Data did not match any of the schemas described in anyOf»":
	 * https://github.com/thehcginstitute-com/m1/issues/567
	 * 4) "`Ebizmarts_MailChimp`: «merge_fields.SHIPPING : Data did not match any of the schemas described in anyOf»":
	 * https://github.com/thehcginstitute-com/m1/issues/568
	 * 2024-05-16
	 * «a JSON object with the required keys `addr1`, `city`, `state`, and `zip`, and the optional keys `addr2` and `country`.
	 * Values for these fields must be strings.»
	 * https://mailchimp.com/developer/marketing/docs/merge-fields#add-merge-data-to-contacts
	 * @used-by self::attCustomer()
	 */
	private function vAddress(string $ac):array {
		$o = $this->o(); /** @var O $o */
		$c = $this->c(); /** @var C $c */
		$c->getPrimaryShippingAddress();
		$c->getDefaultShippingAddress();
		$a = $this->address($ac); /** @var ?AddressC $a */
		return [
			'addr1' => ''
			,'addr2' => ''
			,'city' => ''
			,'country' => ''
			,'state' => ''
			,'zip' => ''
		];
	}

	/**
	 * 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::addressGet()
	 * @return AddressO|AddressC|null
	 */
	private function address(string $ac):?AddressA {return dfc($this, function(string $ac):?AddressC {
		$r = null; /** @var AddressA $r */
		$t = df_assert_address_type(df_first(explode('_', $ac))); /** @var string $t */
		if ($o = $this->o()) {/** @var ?O $o */
			$r = df_oa($o, $t);
		}
		return $r ?: df_ftn($this->c()->getPrimaryAddress("default_$t"));
	}, [$ac]);}

	/**
	 * 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::customizedAttributes()
	 * @used-by self::vAddress()
	 * @param string|Closure $k
	 * @return mixed|null
	 */
	private function addressGet(string $ac, $k) {return !($a = $this->address($ac)) ? null : (
		!is_string($k) ? $k($a) : (df_starts_with($k, 'get') ? call_user_func([$a, $k]) : $a[$k])
	);}

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
	 * @used-by self::c()
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
	private function name(string $k):string {return $this->c()[$k] ?: ($this->sub()["subscriber_$k"] ?: (
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
	private function set($k, $v):void {
		if (!df_nes($v)) {
			$this->_d[$k] = $v;
		}
	}

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