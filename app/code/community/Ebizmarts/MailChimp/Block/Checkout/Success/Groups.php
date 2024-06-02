<?php

/**
 * Checkout subscribe interest groups block renderer
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MailChimp
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Block_Checkout_Success_Groups extends Mage_Core_Block_Template
{
    protected $_currentIntesrest;
    /**
     * @var Ebizmarts_MailChimp_Helper_Data
     */
    protected $_helper;
    protected $_toreId;

    function __construct()
    {
        parent::__construct();
        $this->_helper = hcg_mc_h();
        $this->_storeId = Mage::app()->getStore()->getId();
    }

    /**
     * @return string
     */
    function getFormUrl()
    {
        return $this->getSuccessInterestUrl();
    }

    /**
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    function getSuccessInterestUrl()
    {
        $url = 'mailchimp/group/index';
        return Mage::app()->getStore()->getUrl($url);
    }

    /**
     * @return array|null
     * @throws Mage_Core_Exception
     * @throws MailChimp_Error
     */
    function getInterest() {
        $order = $this->getSessionLastRealOrder();
		$subscriber = df_subscriber($order->getCustomerEmail());
        $subscriberId = $subscriber->getSubscriberId();
        $customerId = $order->getCustomerId();
        $helper = $this->getMailChimpHelper();
        $interest = $helper->getInterestGroups($customerId, $subscriberId, $order->getStoreId());
        return $interest;
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    function getMessageBefore()
    {
        $storeId = $this->_storeId;
        $message = $this->getMailChimpHelper()->getCheckoutSuccessHtmlBefore($storeId);
        return $message;
    }

    /**
     * @return string
     * @throws Mage_Core_Exception
     */
    function getMessageAfter()
    {
        $storeId = $this->_storeId;
        $message = $this->getMailChimpHelper()->getCheckoutSuccessHtmlAfter($storeId);
        return $message;
    }

    /**
     * @param $data
     * @return string
     */
    function escapeQuote($data)
    {
        return $this->getMailChimpHelper()->mcEscapeQuote($data);
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data|Mage_Core_Helper_Abstract
     */
    function getMailChimpHelper()
    {
        return $this->_helper;
    }

    protected function _getStoreId()
    {
        return Mage::app()->getStore()->getId();
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function getSessionLastRealOrder()
    {
        return $this->getMailChimpHelper()->getSessionLastRealOrder();
    }
}
