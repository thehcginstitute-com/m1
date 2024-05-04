<?php
namespace HCG\MailChimp\Tags;
use Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle as InterestGroupHandle;
use Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags as T;
use Ebizmarts_MailChimp_Model_Config as Cfg;
# 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
final class ProcessMergeFields {
	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::p()
	 * @throws \Mage_Core_Exception
	 */
	static function p(array $data, bool $subscribe = false):void {
		$helper = hcg_mc_h();
		$email = $data['email'];
		$listId = $data['list_id'];
		$STATUS_SUBSCRIBED = \Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
		$storeId = $helper->getMagentoStoreIdsByListId($listId)[0];
		$t = new T; /** @var T $t */
		$t->_mailChimpTags = $helper->getMapFields($storeId);
		$t->_mailChimpTags = $helper->unserialize($t->_mailChimpTags);
		$customer = $helper->loadListCustomer($listId, $email);
		if ($customer) {
			$t->setCustomer($customer);
			self::_setMailchimpTagsToCustomer($t, $data);
		}
		$subscriber = $helper->loadListSubscriber($listId, $email);
		$fname = self::_getFName($t, $data);
		$lname = self::_getLName($t, $data);
		if ($subscriber->getId()) {
			if ($subscriber->getStatus() != $STATUS_SUBSCRIBED && $subscribe) {
				$subscriber->setStatus($STATUS_SUBSCRIBED);
				$subscriber->setSubscriberFirstname($fname);
				$subscriber->setSubscriberLastname($lname);
			}
		}
		elseif ($subscribe) {
			$helper->subscribeMember($subscriber);
		}
		else {
			/**
			 * Mailchimp subscriber not currently in magento newsletter subscribers.
			 * Get mailchimp subscriber status and add missing newsletter subscriber.
			 */
			self::_addSubscriberData($subscriber, $fname, $lname, $email, $listId);
		}
		$subscriber->save();
		$t->setSubscriber($subscriber);
		if (isset($data['merges']['GROUPINGS'])) {
			$igh = new InterestGroupHandle; /** @var InterestGroupHandle $igh */
			if ($t->getSubscriber() === null) {
				$igh->setCustomer($t->getCustomer());
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
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
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
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_setMailchimpTagToCustomer()
	 */
	private static function gender($s):int {return dfa(['Male' => T::MALE, 'Female' => T::FEMALE], $s, 0);}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_isAddress()
	 */
	private static function _getAttrbuteCode($attrId) {return
		\Mage::getModel('eav/entity_attribute')->load($attrId)->getAttributeCode()
	;}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::_getFName()
	 * @used-by self::_getLName()
	 */
	private static function _getAttrbuteId($attrCode) {
		$attribute = \Mage::getModel('eav/entity_attribute')
			->getCollection()
			->addFieldToFilter('attribute_code', $attrCode)
			->getFirstItem();
		return $attribute->getId();
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::p()
	 */
	private static function _getFName(T $t, array $data) {
		$attrId = self::_getAttrbuteId('firstname');
		$magentoTag = '';
		foreach ($t->_mailChimpTags as $tag) {
			if ($tag['magento'] == $attrId) {
				$magentoTag = $tag['mailchimp'];
				break;
			}
		}
		return $data['merges'][$magentoTag];
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::p()
	 */
	private static function _getLName(T $t, array $data) {
		$attrId = self::_getAttrbuteId('lastname');
		$magentoTag = '';
		foreach ($t->_mailChimpTags as $tag) {
			if ($tag['magento'] == $attrId) {
				$magentoTag = $tag['mailchimp'];
				break;
			}
		}
		return $data['merges'][$magentoTag];
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
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
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by self::p()
	 */
	private static function _setMailchimpTagsToCustomer(T $t, array $data):void {
		$customer = $t->getCustomer();
		foreach ($data['merges'] as $key => $value) {
			if (!empty($value)) {
				if (is_array($t->_mailChimpTags)) {
					if ($key !== 'GROUPINGS') {
						self::_setMailchimpTagToCustomer($key, $value, $t->_mailChimpTags, $customer);
					}
				}
			}
		}
		$customer->save();
	}

	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
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
						$customer->setData('gender', self::gender($value));
					}
				}
			}
		}
	}
}