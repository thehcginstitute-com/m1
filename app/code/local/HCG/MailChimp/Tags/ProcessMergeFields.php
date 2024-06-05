<?php
namespace HCG\MailChimp\Tags;
use Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle as InterestGroupHandle;
use Ebizmarts_MailChimp_Model_Config as Cfg;
use HCG\MailChimp\Tags as T;
use Mage_Customer_Model_Customer as C;
use Mage_Newsletter_Model_Subscriber as Sub;
# 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
final class ProcessMergeFields {
	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::p()
	 * @throws \Mage_Core_Exception
	 */
	static function p(array $data, bool $subscribe = false):void {
		$t = new T; /** @var T $t */
		$i = new self($data, $t); /** @var self $i */
		$email = $data['email'];
		$listId = $data['list_id'];
		$t->set();
		if ($i->customer()) {
			$i->_setMailchimpTagsToCustomer();
		}
		$sub = hcg_mc_sub($listId, $email); /** @var Sub $sub */
		# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
		[$fname, $lname] = [$i->mcByCA('firstname'), $i->mcByCA('lastname')]; /** @var string $fname */ /** @var string $lname */
		if ($sub->getId()) {
			if ($sub->getStatus() != Sub::STATUS_SUBSCRIBED && $subscribe) {
				$sub->setStatus(Sub::STATUS_SUBSCRIBED);
				$sub->setSubscriberFirstname($fname);
				$sub->setSubscriberLastname($lname);			
			}
		}
		elseif ($subscribe) {
			hcg_mc_h()->subscribeMember($sub);
		}
		else {
			# Mailchimp subscriber not currently in magento newsletter subscribers.
			# Get mailchimp subscriber status and add missing newsletter subscriber.
			self::_addSubscriberData($sub, $fname, $lname, $email, $listId);
		}
		$sub->save();
		$t->setSubscriber($sub);
		if (isset($data['merges']['GROUPINGS'])) {
			$igh = new InterestGroupHandle; /** @var InterestGroupHandle $igh */
			if ($t->getSubscriber() === null) {
				$igh->setCustomer($i->customer());
			}
			else {
				$igh->setSubscriber($t->getSubscriber());
			}
			$igh
				->setGroupings($data['merges']['GROUPINGS'])
				->setListId($listId)
				->processGroupsData()
			;
		}
	}

	/**
	 * 2024-05-04
	 * @used-by self::p()
	 */
	private function __construct(array $data, T $t) {$this->_d = $data; $this->_t = $t;}

	/**
	 * 2024-05-04
	 * @used-by self::_setMailchimpTagsToCustomer()
	 * @used-by self::p()
	 */
	private function customer():?C {return dfc($this, function():?C {return df_customer($this->_d['email']);});}

	/**
	 * 2024-06-02
	 * @used-by self::p()
	 */
	private function mcByCA(string $a) {return $this->_d['merges'][df_assert($this->_t->mcByCA($a))];}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::p()
	 */
	private function _setMailchimpTagsToCustomer():void {
		$customer = $this->customer();
		foreach ($this->_d['merges'] as $key => $value) {
			if (!empty($value)) {
				if (is_array($this->_t->get())) {
					if ($key !== 'GROUPINGS') {
						self::_setMailchimpTagToCustomer($key, $value, $this->_t->get(), $customer);
					}
				}
			}
		}
		$customer->save();
	}

	/**
	 * 2024-05-04
	 * @used-by self::__construct()
	 * @used-by self::mcByCA()
	 * @used-by self::_setMailchimpTagsToCustomer()
	 * @used-by self::customer()
	 * @var array
	 */
	private $_d;

	/**
	 * 2024-05-04
	 * @used-by self::__construct()
	 * @used-by self::mcByCA()
	 * @used-by self::_setMailchimpTagsToCustomer()
	 * @var T
	 */
	private $_t;

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::p()
	 */
	private static function _addSubscriberData($subscriber, $fname, $lname, $email, $listId):void {
		$h = hcg_mc_h();
		$scopeArray = $h->getFirstScopeFromConfig(Cfg::GENERAL_LIST, $listId);
		$api = $h->getApi($scopeArray['scope_id'], $scopeArray['scope']);
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
				$h->subscribeMember($subscriber);
			}
			elseif (
				'unsubscribed' === $member['status']
				&& !hcg_mc_h_webhook()->getWebhookDeleteAction($subscriber->getStoreId())
			) {
				$h->unsubscribeMember($subscriber);
			}
		}
		catch (\MailChimp_Error $e) {
			$h->logError($e->getFriendlyMessage());
		}
		catch (\Exception $e) {
			$h->logError($e->getMessage());
		}
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::_isAddress()
	 */
	private static function _getAttrbuteCode($attrId) {return
		\Mage::getModel('eav/entity_attribute')->load($attrId)->getAttributeCode()
	;}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::_setMailchimpTagToCustomer()
	 */
	private static function _isAddress($attrId):bool {
		if (is_numeric($attrId)) {
			// Gets the magento attr_code.
			$attributeCode = self::_getAttrbuteCode($attrId);
			if ($attributeCode == 'default_billing' || $attributeCode == 'default_shipping') {
				return true;
			}
		}
		return false;
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/thehcginstitute-com/m1/issues/589
	 * @used-by self::_setMailchimpTagsToCustomer()
	 */
	private static function _setMailchimpTagToCustomer($key, $value, $mapFields, $customer):void {
		$ignore = [
			'billing_company', 'billing_country', 'billing_zipcode', 'billing_state', 'billing_telephone',
			'shipping_company', 'shipping_telephone', 'shipping_country', 'shipping_zipcode', 'shipping_state',
			'dop', 'store_code'
		];
		foreach ($mapFields as $map) {
			if ($map['mailchimp'] == $key) {
				if (!in_array($map['magento'], $ignore) && !self::_isAddress($map['magento'])) {
					if ($key != 'GENDER') {
						$customer->setData($map['magento'], $value);
					} else {
						$customer->setData('gender', df_gender_s2i($value));
					}
				}
			}
		}
	}
}