<?php
namespace HCG\MailChimp\Tags;
use Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags as T;
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
		$t->_mailChimpTags = $t->unserializeMapFields($t->_mailChimpTags);
		$customer = $helper->loadListCustomer($listId, $email);
		if ($customer) {
			$t->setCustomer($customer);
			$t->_setMailchimpTagsToCustomer($data);
		}
		$subscriber = $helper->loadListSubscriber($listId, $email);
		$fname = $t->_getFName($data);
		$lname = $t->_getLName($data);
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
			$t->_addSubscriberData($subscriber, $fname, $lname, $email, $listId);
		}
		$subscriber->save();
		$t->setSubscriber($subscriber);
		if (isset($data['merges']['GROUPINGS'])) {
			$interestGroupHandle = $t->_getInterestGroupHandleModel();
			if ($t->getSubscriber() === null) {
				$interestGroupHandle->setCustomer($t->getCustomer());
			}
			else {
				$interestGroupHandle->setSubscriber($t->getSubscriber());
			}
			$interestGroupHandle->setGroupings($data['merges']['GROUPINGS'])
				->setListId($listId)
				->processGroupsData();
		}
	}
}