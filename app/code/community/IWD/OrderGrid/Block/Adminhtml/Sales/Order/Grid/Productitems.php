<?php

class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Productitems extends Mage_Adminhtml_Block_Widget
{
    function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordergrid/grid/product_items.phtml');
    }

    function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }

    protected function getOrder()
    {
        $orderId = $this->getData('order_id');
        return Mage::getModel('sales/order')->load($orderId);
    }
}