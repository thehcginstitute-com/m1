<?php

class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Ordereditems extends Mage_Adminhtml_Block_Widget
{
    function __construct()
    {
        parent::__construct();
        $this->setTemplate('iwd/ordergrid/grid/ordered_items.phtml');
    }

    function getSelectionAttributes($item)
    {
        if ($item instanceof Mage_Sales_Model_Order_Item) {
            $options = $item->getProductOptions();
        } else {
            $options = $item->getOrderItem()->getProductOptions();
        }

        if (isset($options['bundle_selection_attributes'])) {
            return unserialize($options['bundle_selection_attributes']);
        }

        return null;
    }

    function isChildCalculated($item)
    {
        if ($item) {
            if ($parentItem = $item->getParentItem()) {
                if ($options = $parentItem->getProductOptions()) {
                    return (isset($options['product_calculations']) && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD);
                }
            } else {
                if ($options = $item->getProductOptions()) {
                    return !(isset($options['product_calculations']) && $options['product_calculations'] == Mage_Catalog_Model_Product_Type_Abstract::CALCULATE_CHILD);
                }
            }
        }

        return false;
    }

    function canShowPriceInfo($item)
    {
        return (($item->getParentItem() && $this->isChildCalculated($item))
            || (!$item->getParentItem() && !$this->isChildCalculated($item)));
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