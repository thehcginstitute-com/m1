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
# "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
final class Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags {
	/**
	 * 2024-06-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * 2) https://3v4l.org/akQm0#tabs
	 * @used-by self::p()
	 * @used-by self::mcByMG()
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_setMailchimpTagsToCustomer()
	 * @return array(string => array(string => string)
	 */
	function get():array {return $this->_d;}

	/**
	 * 2024-06-02
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::mcByCA()
	 */
	function mcByCA(string $a):?string {return $this->mcByMG(df_att_code2id($a));}

	/**
	 * 2024-06-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::p()
	 */
	function set():void {$this->_d = hcg_mc_cfg_fields();}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::makeMailchimpTagsBatchStructure()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_buildSubscriberData()
	 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::updateSubscriber()
	 */
	static function p(Sub $sub, int $sid):array {
		$i = new self;
		$i->_sub = $sub;
		$i->_sid = $sid;
		$i->_p();
		return $i->get();
	}

	/**
	 * 2024-05-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::p()
	 */
	private function _p():void {
		# 2024-05-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# https://3v4l.org/akQm0#tabs
		foreach (hcg_mc_cfg_fields() as $f) {/** @var array(string => string) $f */
			if (
				($mg = dfa($f, 'magento')) /** @var string $mg */
				&& ($mc = dfa($f, 'mailchimp')) /** @var string $mc */
				/** @var string|null|array(string => string) $v */
				&& !df_nes($v = is_numeric($mg) ? $this->attCustomer(df_customer_att($mg)) : $this->attOther($mg))
			) {
				$this->_d[strtoupper($mc)] = $v;
			}
		}
	}

	/**
	 * 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::addressGet()
	 * @used-by self::vAddress()
	 * @return AddressO|AddressC|null
	 */
	private function address(string $ac):?AddressA {return dfc($this, function(string $ac):?AddressA {
		$r = null; /** @var AddressA $r */
		$t = df_assert_address_type(df_first(explode('_', $ac))); /** @var string $t */
		if ($o = $this->o()) {/** @var ?O $o */
			$r = df_oa($o, $t);
		}
		return $r ?: df_ftn($this->c()->getPrimaryAddress("default_$t"));
	}, [$ac]);}

	/**
	 * 2024-05-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::attOther()
	 * @used-by self::vAddress()
	 * @param string|Closure $k
	 * @return mixed|null
	 */
	private function addressGet(string $ac, $k) {return !($a = $this->address($ac)) ? null : (
		!is_string($k) ? $k($a) : (df_starts_with($k, 'get') ? call_user_func([$a, $k]) : $a[$k])
	);}

	/**
	 * 2024-05-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::_p()
	 * @param IA|A $a
	 * @return string|null|array(string => string)
	 */
	private function attCustomer(IA $a) { /** @var string|null|array(string => string) $r */
		$c = $this->c(); /** @var C $c */
		$sid = $this->sid(); /** @var int $sid */
		$sub = $this->sub(); /** @var Sub $sub */
		switch ($ac = $a->getAttributeCode()) {/** @var string $ac */
			case 'default_billing':
			case 'default_shipping':
				$r = $this->vAddress($ac);
				break;
			case 'gender':
				$r = df_gender_s($c);
				break;
			case 'group_id':
				$r = df_customer_group_name($c);
				break;
			case 'firstname':
			case 'lastname':
				# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# "`Ebizmarts_MailChimp`: «Your merge fields were invalid» /
				# «field [FNAME] : Please enter a value» /
				# «field [LNAME] : Please enter a value»": https://github.com/thehcginstitute-com/m1/issues/507
				# 2024-05-20
				# 1) https://us7.admin.mailchimp.com/lists/settings/merge-tags?id=146033
				# 2) "Provide an ability to specify a context for a `Df\Core\Exception` instance":
				# https://github.com/mage2pro/core/issues/375
				df_assert(
					$r = $c[$ac] ?: ($sub["subscriber_$ac"] ?: (
						($o = $this->o()) ? $o["customer_$ac"] : null
					))
					,df_error_create("The required field `{$ac}` is empty for the customer", [
						'Customer' => $c, 'Subscriber' => $sub
					])
				);
				break;
			case 'store_id':
				$r = $sid;
				break;
			case 'website_id':
				$r = df_store($sid)->getWebsiteId();
				break;
			case 'created_in':
				$r = df_store($sid)->getName();
				break;
			case 'dob':
				$r = !($v = $c->getDob()) ? null : hcg_mc_h_date()->formatDate($v, 'm/d', 1);
				break;
			default:
				$r = 'email' === $ac ? null : df_att_val($c, $a);
		}
		return $r;
	}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::buildattOther()
	 * @return string|null
	 */
	private function attOther(string $mg) {
		$r = null; /** @var string|null|array(string => string) $r */
		$addressGet = function($f) use($mg) {/** @var string|Closure $f */return $this->addressGet($mg, $f);};
		switch ($mg) {
			case 'billing_company':
			case 'shipping_company':
				$r = $addressGet('company');
				break;
			case 'billing_telephone':
			case 'shipping_telephone':
				$r = $addressGet('telephone');
				break;
			case 'billing_country':
			case 'shipping_country':
				$r = $addressGet(function(AddressA $a):string {return df_country_ctn($a->getCountry(), '');});
				break;
			case 'billing_zipcode':
			case 'shipping_zipcode':
				$r = $addressGet('postcode');
				break;
			case 'billing_state':
			case 'shipping_state':
				$r = $addressGet('getRegion'); /** @uses Mage_Customer_Model_Address_Abstract::getRegion() */
				break;
			case 'dop':
				$r = !($o = $this->o()) ? null : $o->getCreatedAt();
				break;
			case 'store_code':
				$r = df_store($this->sid())->getCode();
		}
		return $r;
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::_p()
	 * @used-by self::address()
	 * @used-by self::attCustomer()
	 * @used-by self::attOther()
	 */
	private function c():C {return dfc($this, function() {return df_customer($this->sub());});}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::address()
	 * @used-by self::attCustomer()
	 * @used-by self::attOther()
	 */
	private function o():?O {return dfc($this, function() {/** @var OC $c */ return !count(
		$c = df_order_c()
			->addFieldToFilter('customer_email', ['eq' => $this->sub()->getSubscriberEmail()])
			->setOrder('created_at', 'DESC')
			->setPageSize(1)
	) ? null : $c->getLastItem();});}

	/**
	 * 2024-06-02 https://3v4l.org/p0kkb
	 * @used-by self::mcByCA()
	 */
	private function mcByMG(string $mg):?string {return dfa(
		df_find($this->get(), function(array $a) use($mg):bool {return $mg === (string)dfa($a, 'magento');})
		,'mailchimp'
	);}

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::attCustomer()
	 * @used-by self::addCreatedIn()
	 * @used-by self::addStoreCodeFromCustomizedAttribute()
	 * @used-by self::addWebsiteId()
	 * @used-by self::_p()
	 * @used-by self::c()
	 */
	private function sid():int {return $this->_sid;}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::_p()
	 * @used-by self::attCustomer()
	 * @used-by self::o()
	 */
	private function sub():Sub {return $this->_sub;}

	/**
	 * 2024-05-15 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * 2) https://mailchimp.com/developer/marketing/docs/merge-fields#add-merge-data-to-contacts
	 * 3) "`Ebizmarts_MailChimp`: «merge_fields.BILLING : Data did not match any of the schemas described in anyOf»":
	 * https://github.com/thehcginstitute-com/m1/issues/567
	 * 4) "`Ebizmarts_MailChimp`: «merge_fields.SHIPPING : Data did not match any of the schemas described in anyOf»":
	 * https://github.com/thehcginstitute-com/m1/issues/568
	 * 2024-05-16
	 * 1) «a JSON object with the required keys `addr1`, `city`, `state`, and `zip`, and the optional keys `addr2` and `country`.
	 * Values for these fields must be strings.»
	 * https://mailchimp.com/developer/marketing/docs/merge-fields#add-merge-data-to-contacts
	 * 2) https://3v4l.org/ebbhT
	 * @used-by self::attCustomer()
	 */
	private function vAddress(string $ac):?array {return
		!($a = $this->address($ac)  /** @var AddressO|AddressC|null $a */) ? null : [
			'addr1' => $a->getStreet(1)
			,'addr2' => $a->getStreet(2)
			,'city' => $a->getCity()
			,'country' => df_country_ctn($a->getCountry(), '')
			,'state' => $a->getRegion()
			,'zip' => $a->getPostcode()
		]
	;}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * 2024-06-02 https://3v4l.org/akQm0#tabs
	 *		{
	 *			"_1468601283719_719": {"mailchimp": "WEBSITE", "magento": "1"},
	 *			"_1468609069544_544": {"mailchimp": "STOREID", "magento": "2"},
	 *			"_1469026825907_907": {"mailchimp": "STORECODE", "magento": "3"},
	 *			"_1469027411717_717": {"mailchimp": "PREFIX", "magento": "4"},
	 *			"_1469027418285_285": {"mailchimp": "FNAME", "magento": "5"},
	 *			"_1469027422918_918": {"mailchimp": "MNAME", "magento": "6"},
	 *			"_1469027429502_502": {"mailchimp": "LNAME", "magento": "7"},
	 *			"_1469027434574_574": {"mailchimp": "SUFFIX", "magento": "8"},
	 *			"_1469027444231_231": {"mailchimp": "EMAIL", "magento": "9"},
	 *			"_1469027462887_887": {"mailchimp": "DOB", "magento": "11"},
	 *			"_1469027468903_903": {"mailchimp": "BILLING", "magento": "13"},
	 *			"_1469027475632_632": {"mailchimp": "SHIPPING", "magento": "14"},
	 *			"_1469027480560_560": {"mailchimp": "TAX", "magento": "15"},
	 *			"_1469027486920_920": {"mailchimp": "CONFIRMED", "magento": "16"},
	 *			"_1469027496512_512": {"mailchimp": "CREATEDAT", "magento": "17"},
	 *			"_1469027502720_720": {"mailchimp": "GENDER", "magento": "18"},
	 *			"_1469027508616_616": {"mailchimp": "DISGRPCHG", "magento": "35"},
	 *			"_1472845935735_735": {"mailchimp": "BCOMPANY", "magento": "billing_company"},
	 *			"_1472846546252_252": {"mailchimp": "BCOUNTRY", "magento": "billing_country"},
	 *			"_1472846569989_989": {"mailchimp": "BTELEPHONE", "magento": "billing_telephone"},
	 *			"_1472846572949_949": {"mailchimp": "BZIPCODE", "magento": "billing_zipcode"},
	 *			"_1472846578861_861": {"mailchimp": "SCOMPANY", "magento": "shipping_company"},
	 *			"_1472846584014_14": {"mailchimp": "SCOUNTRY", "magento": "shipping_country"},
	 *			"_1472846587534_534": {"mailchimp": "STELEPHONE", "magento": "shipping_telephone"},
	 *			"_1472846591374_374": {"mailchimp": "SZIPCODE", "magento": "shipping_zipcode"}
	 *		}
	 * @used-by self::_p()
	 * @used-by self::get()
	 * @used-by self::set()
	 * @var array(string => array(string => string)
	 */
	private $_d;

	/**
	 * @var int
	 */
	private $_sid;

	/**
	 * 2024-05-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::p()
	 * @used-by self::sub()
	 * @var Sub
	 */
	private $_sub;
}