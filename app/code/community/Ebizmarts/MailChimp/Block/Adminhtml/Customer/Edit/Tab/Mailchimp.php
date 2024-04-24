<?php
# 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp`":
# https://github.com/thehcginstitute-com/m1/issues/579
class Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp extends Mage_Adminhtml_Block_Widget_Grid
{

	protected $_lists = array();
	protected $_info = array();
	protected $_myLists = array();
	protected $_generalList = array();
	protected $_form;
	protected $_api;
	protected $_customer;
	/**
	 * @var Ebizmarts_MailChimp_Helper_Data
	 */
	protected $_helper;
	protected $_storeId;

	function __construct()
	{
		parent::__construct();
		$this->setTemplate('ebizmarts/mailchimp/customer/tab/mailchimp.phtml');
		$this->_helper = $this->makeHelper();
		$customerId = (int) $this->getRequest()->getParam('id');
		if ($customerId) {
			$this->_customer = $this->getCustomerModel()->load($customerId);
			$this->_storeId = $this->getCustomer()->getStoreId();
		}
	}

	function getInterest()
	{
		$customer = $this->getCustomer();
		$subscriber = $this->getSubscriberModel();
		$subscriber->loadByEmail($customer->getEmail());
		$subscriberId = $subscriber->getSubscriberId();
		$customerId = $customer->getId();
		$storeId = $this->getStoreId();
		$interest = $this->_helper->getInterestGroups($customerId, $subscriberId, $storeId);

		return $interest;
	}

	/**
	 * @param $data
	 * @return string
	 */
	function escapeQuote($data)
	{
		return $this->getHelper()->mcEscapeQuote($data);
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	function getHelper($type='')
	{
		return $this->_helper;
	}

	/**
	 * @return Mage_Newsletter_Model_Subscriber
	 */
	protected function getSubscriberModel()
	{
		return Mage::getModel('newsletter/subscriber');
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function makeHelper() {return hcg_mc_h();}

	/**
	 * @return false|Mage_Core_Model_Abstract
	 */
	protected function getCustomerModel()
	{
		return Mage::getModel('customer/customer');
	}

	/**
	 * @return Mage_Core_Model_Abstract
	 */
	protected function getCustomer()
	{
		return $this->_customer;
	}

	/**
	 * 2024-04-24 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Delete the `->getMailchimpStoreView()` / `mailchimp_store_view` calls for `Mage_Customer_Model_Customer`
	 * because it always returns `NULL`": https://github.com/thehcginstitute-com/m1/issues/578
	 */
	protected function getStoreId():int {return $this->_storeId;}
}
