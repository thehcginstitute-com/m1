<?php
# 2024-06-06 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
class Ebizmarts_MailChimp_GroupController extends Mage_Core_Controller_Front_Action {
	function indexAction() {
		$helper = $this->getHelper();
		$order = $this->getSessionLastRealOrder();
		$session = $this->getCoreSession();
		$interestGroup = $this->getInterestGroupModel();
		$params = $this->getRequest()->getParams();
		$storeId = $order->getStoreId();
		$customerEmail = $order->getCustomerEmail();
		$customerId = $order->getCustomerId();
		$subscriber = df_subscriber($customerEmail);
		try {
			if (!$subscriber->getSubscriberId()) {
				$subscriber->setSubscriberEmail($customerEmail);
				$subscriber->setSubscriberFirstname($order->getCustomerFirstname());
				$subscriber->setSubscriberLastname($order->getCustomerLastname());
				$subscriber->subscribe($customerEmail);
			}
			$subscriberId = $subscriber->getSubscriberId();
			$interestGroup->getByRelatedIdStoreId($customerId, $subscriberId, $storeId);
			$encodedGroups = $helper->arrayEncode($params);
			$interestGroup->setGroupdata($encodedGroups);
			$interestGroup->setSubscriberId($subscriberId);
			$interestGroup->setCustomerId($customerId);
			$interestGroup->setStoreId($storeId);
			$interestGroup->setUpdatedAt($this->getCurrentDateTime());
			$interestGroup->save();
			$this->getApiSubscriber()->update($subscriber->getSubscriberEmail(), $storeId, '', 1);
			$session->addSuccess($this->__('Thanks for sharing your interest with us.'));
		}
		catch (Exception $e) {
			df_log($e);
			$session->addWarning(
				$this->__(
					'Something went wrong with the interests subscription. '
					. 'Please go to the account subscription menu to subscriber to the interests successfully.'
				)
			);
		}
		$this->_redirect('/');
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data|Mage_Core_Helper_Abstract
	 */
	protected function getHelper($type='mailchimp')
	{
		return Mage::helper($type);
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Subscribers
	 */
	protected function getApiSubscriber()
	{
		return Mage::getModel('mailchimp/api_subscribers');
	}

	/**
	 * @return Mage_Sales_Model_Order
	 */
	protected function getSessionLastRealOrder()
	{
		return $this->getHelper()->getSessionLastRealOrder();
	}

	/**
	 * @return Mage_Core_Model_Session
	 */
	protected function getCoreSession()
	{
		return Mage::getSingleton('core/session');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Interestgroup
	 */
	protected function getInterestGroupModel()
	{
		return Mage::getModel('mailchimp/interestgroup');
	}

	/**
	 * @return string
	 */
	protected function getCurrentDateTime()
	{
		return hcg_mc_h_date()->formatDate(null, 'd-m-Y H:i:s');
	}
}
