<?php

/**
 * Class IWD_AdminCheckout_Model_Observer
 */
class IWD_AdminCheckout_Model_Observer
{
    /**
     * Check required modules:
     *   - IWD_ALL
     */
    function checkRequiredModules()
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                $message = 'Please setup IWD_ALL in order to finish <strong>IWD Admin Checkout</strong> installation.';
                $this->addMessage($message);
            } else {
                $version = Mage::getConfig()->getModuleConfig('IWD_All')->version;
                if (version_compare($version, "2.0.0", "<")) {
                    $message = 'Please update IWD_ALL extension because some features of <strong>IWD Admin Checkout</strong> can be not available.';
                    $this->addMessage($message);
                }
            }
        }
    }

    protected function addMessage($message)
    {
        $cache = Mage::app()->getCache();

        $iwdAllUrl = 'https://www.iwdagency.com/modules/iwd_all.tgz';
        $iwdUserGuideUrl = 'https://www.iwdagency.com/help/';

        $noticeMessage = 'Important: ' . $message . '<br />' .
            'Please download <a href="' . $iwdAllUrl . '" target="_blank">IWD_ALL</a> and set it up via Magento Connect.<br />' .
            'Please refer link to <a href="' .$iwdUserGuideUrl . '" target="_blank">installation guide</a>';

        if ($cache->load("iwd_order_manager") === false) {
            Mage::getSingleton('adminhtml/session')->addNotice($noticeMessage);
        }

        $cache->save('true', 'iwd_order_manager', array("iwd_order_manager"), $lifeTime = 5);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    function initQuoteAddress(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_admin_checkout')->isCustomCreationProcess()) {
            return;
        }

        /**
         * @var $sessionQuote Mage_Adminhtml_Model_Session_Quote
         */
        $sessionQuote = $observer->getEvent()->getData("session_quote");
        $quote = $sessionQuote->getQuote();
        $customer = $sessionQuote->getCustomer(true);
        $customerId = $customer->getId();

        if ($customerId) {
            $quoteAddress = $quote->getShippingAddress();
            if ($customerId != $quoteAddress->getCustomerId()) {
                $defaultAddress = $customer->getDefaultShippingAddress();
                $this->updateQuoteAddress($quoteAddress, $defaultAddress);
            }

            $quoteAddress = $quote->getBillingAddress();
            if ($customerId != $quoteAddress->getCustomerId()) {
                $defaultAddress = $customer->getDefaultBillingAddress();
                $this->updateQuoteAddress($quoteAddress, $defaultAddress);
            }

            $quote->setCustomer($customer)->save();
        }
    }

    /**
     * @param $quoteAddress
     * @param $defaultAddress
     */
    protected function updateQuoteAddress($quoteAddress, $defaultAddress)
    {
        if ($quoteAddress && $quoteAddress->getId() && $defaultAddress && $defaultAddress->getId()) {
            $customerId = $quoteAddress->getCustomerId();
            $customerAddressId = $quoteAddress->getCustomerAddressId();
            if (empty($customerAddressId) || $customerAddressId != $defaultAddress->getId()) {
                $quoteAddress->setCustomerAddressId($defaultAddress->getId())->setCustomerId($customerId);
                $quoteAddress->addData($defaultAddress->getData());
                $quoteAddress->save();
            }
        }
    }

    function beforeOrderCreateLoadBlock()
    {
        if (!Mage::helper('iwd_admin_checkout')->isCustomCreationProcess()) {
            return;
        }

        $this->recollectShipping();
        $this->selectDefaultShippingMethod();
    }

    protected function recollectShipping()
    {
        /**
         * @var $quote Mage_Sales_Model_Quote
         */
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $request = Mage::app()->getRequest();

        $request->setPost('reset_shipping', 0);
        $quote->getShippingAddress()->setCollectShippingRates(true)->save();
    }

    protected function selectDefaultShippingMethod()
    {
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $request = Mage::app()->getRequest();

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $order = Mage::app()->getRequest()->getParam('order', array());

        if (empty($shippingMethod) && !isset($order['shipping_method'])) {
            $order = $request->getPost('order');
            $order['shipping_method'] = Mage::helper('iwd_admin_checkout')->getDefaultShippingMethod();
            $request->setPost('order', $order);
        }
    }
}
