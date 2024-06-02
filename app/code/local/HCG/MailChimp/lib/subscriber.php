<?php
use Mage_Newsletter_Model_Subscriber as S;
/**
 * 2024-06-02 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/cabinetsbay/site/issues/524
 * @used-by Ebizmarts_MailChimp_Model_Observer::newOrder()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_clean()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_updateEmail()
 * @used-by Ebizmarts_MailChimp_Model_ProcessWebhook::_unsubscribe()
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::p()
 */
function hcg_mc_sub($listId, string $email) {
	$subscriber = null;
	$storeIds = hcg_mc_h()->getMagentoStoreIdsByListId($listId);
	//add store id 0 for those created from the back end.
	$storeIds[] = 0;
	if (!empty($storeIds)) {
		$subscriber = Mage::getModel('newsletter/subscriber')->getCollection()
			->addFieldToFilter('store_id', array('in' => $storeIds))
			->addFieldToFilter('subscriber_email', $email)
			->setPageSize(1)->getLastItem();
		if (!$subscriber->getId()) {
			/**
			 * No subscriber exists. Try to find a customer based
			 * on email address for the given stores instead.
			 */
			$subscriber = Mage::getModel('newsletter/subscriber');
			$subscriber->setEmail($email);
			$customer = hcg_mc_h()->loadListCustomer($listId, $email);
			if ($customer) {
				$subscriber->setStoreId($customer->getStoreId());
				$subscriber->setCustomerId($customer->getId());
			} else {
				/**
				 * No customer with that address. Just assume the first
				 * store ID is the correct one as there is no other way
				 * to tell which store this mailchimp list guest subscriber
				 * belongs to.
				 */
				$subscriber->setStoreId($storeIds[0]);
			}
		}
	}

	return $subscriber;
}