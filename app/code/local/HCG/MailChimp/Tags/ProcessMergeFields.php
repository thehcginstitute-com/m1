<?php
namespace HCG\MailChimp\Tags;
# 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
final class ProcessMergeFields {
	/**
	 * 2024-05-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags`": https://github.com/cabinetsbay/site/issues/589
	 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::p()
	 * @param $data
	 * @throws \Mage_Core_Exception
	 */
	function p($data, bool $subscribe = false):void {
		$helper = hcg_mc_h();
		$email = $data['email'];
		$listId = $data['list_id'];
		$STATUS_SUBSCRIBED = \Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED;
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
		}
		elseif ($subscribe) {
			$helper->subscribeMember($subscriber);
		}
		else {
			/**
			 * Mailchimp subscriber not currently in magento newsletter subscribers.
			 * Get mailchimp subscriber status and add missing newsletter subscriber.
			 */
			$this->_addSubscriberData($subscriber, $fname, $lname, $email, $listId);
		}
		$subscriber->save();
		$this->setSubscriber($subscriber);
		if (isset($data['merges']['GROUPINGS'])) {
			$interestGroupHandle = $this->_getInterestGroupHandleModel();
			if ($this->getSubscriber() === null) {
				$interestGroupHandle->setCustomer($this->getCustomer());
			}
			else {
				$interestGroupHandle->setSubscriber($this->getSubscriber());
			}
			$interestGroupHandle->setGroupings($data['merges']['GROUPINGS'])
				->setListId($listId)
				->processGroupsData();
		}
	}
}