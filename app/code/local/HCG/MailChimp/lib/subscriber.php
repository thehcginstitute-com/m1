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
function hcg_mc_sub($listId, string $email):S {
	$storeIds = array_merge(hcg_mc_h()->getMagentoStoreIdsByListId($listId), [0]);
	$r = Mage::getModel('newsletter/subscriber')->getCollection()
		->addFieldToFilter('store_id', ['in' => $storeIds])
		->addFieldToFilter('subscriber_email', $email)
		->setPageSize(1)->getLastItem(); /** @var S $r */
	if (!$r->getId()) {
		/**
		 * No subscriber exists. Try to find a customer based
		 * on email address for the given stores instead.
		 */
		$r = Mage::getModel('newsletter/subscriber');
		$r->setEmail($email);
		$customer = hcg_mc_h()->loadListCustomer($listId, $email);
		if ($customer) {
			$r->setStoreId($customer->getStoreId());
			$r->setCustomerId($customer->getId());
		}
		else {
			/**
			 * No customer with that address. Just assume the first
			 * store ID is the correct one as there is no other way
			 * to tell which store this mailchimp list guest subscriber
			 * belongs to.
			 */
			$r->setStoreId($storeIds[0]);
		}
	}
	return $r;
}