<?php

class IWD_OrderManager_Block_Adminhtml_Sales_Order_Payment_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form
{
    /**
     * @var null|Mage_Sales_Model_Quote
     */
    protected $quote = null;

    /**
     * IWD_OrderManager_Block_Adminhtml_Sales_Order_Payment_Form constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/payment/form.phtml');
    }

    /**
     * @return Mage_Core_Model_Abstract|Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        $orderId = $this->getOrderId();
        return Mage::getModel("sales/order")->load($orderId);
    }

    /**
     * @return int
     */
    protected function getOrderId()
    {
        return Mage::app()->getRequest()->getParam('order_id', 0);
    }

    /**
     * @return Mage_Sales_Model_Quote|null
     */
    public function getQuote()
    {
        if ($this->quote !== null) {
            return $this->quote;
        }

        $orderId = $this->getOrderId();
        if (!isset($orderId) || empty($orderId)) {
            return $this->quote = parent::getQuote();
        }

        $order = $this->getOrder();
        if (empty($order) || !$order->getData()) {
            return null;
        }

        $quote = Mage::getModel('sales/quote')->setStore($order->getStore())->load($order->getQuoteId());
        if (!empty($quote)) {
            $entityId = $quote->getEntityId();
            if (!empty($entityId)) {
                $this->quote = $quote;
            }
        } else {
            $quote = Mage::getModel('iwd_ordermanager/order_converter')->convertOrderToQuote($orderId);
            if (empty($quote)) {
                return null;
            }

            $quote->setBaseSubtotal($order->getBaseSubtotal());
        }

        return $this->quote;
    }

    /**
     * @return string
     */
    public function getSelectedMethodCode()
    {
        try {
            if ($this->getOrder() && $this->getOrder()->getPayment()) {
                return $this->getOrder()->getPayment()->getMethod();
            }
        } catch (\Exception $e) {
            return parent::getSelectedMethodCode();
        }

        return parent::getSelectedMethodCode();
    }
}
