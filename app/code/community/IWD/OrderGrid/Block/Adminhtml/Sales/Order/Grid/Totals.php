<?php

/**
 * Class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Totals
 */
class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Totals extends Mage_Adminhtml_Block_Template
{
    /**
     * @return array
     */
    function getTotalSets()
    {
        $helper = Mage::helper('iwd_ordergrid');

        return array(
            'tax'      => array('label' => $helper->__('Tax Total'), 'page_label' => $helper->__('Tax Page Total')),
            'invoiced' => array('label' => $helper->__('Invoiced Total'), 'page_label' => $helper->__('Invoiced Page Total')),
            'shipped'  => array('label' => $helper->__('Shipping Total'), 'page_label' => $helper->__('Shipping Page Total')),
            'refunded' => array('label' => $helper->__('Refunds Total'), 'page_label' => $helper->__('Refunds Page Total')),
            'discount' => array('label' => $helper->__('Coupons Total'), 'page_label' => $helper->__('Coupons Page Total'))
        );
    }

    /**
     * @return array
     */
    function getSelectedTotalSets()
    {
        $totalSets = $this->getTotalSets();
        $selectedSets = $this->getSelectedOrderTotalSets();

        foreach ($totalSets as $id => $set) {
            if (!in_array($id, $selectedSets)) {
                unset($totalSets[$id]);
            }
        }

        return $totalSets;
    }

    /**
     * @return array
     */
    protected function getSelectedOrderTotalSets()
    {
        $orderSets = Mage::getStoreConfig(IWD_OrderGrid_Helper_Data::CONFIG_XPATH_ORDER_TOTALS_SETS);
        return explode(',', $orderSets);
    }

    /**
     * @return mixed
     */
    function getGridOptions()
    {
        return Mage::getModel('iwd_ordergrid/order_totals')->getGridOptions();
    }

    /**
     * @return mixed
     */
    function getTotals()
    {
        return Mage::getModel('iwd_ordergrid/order_totals')->getTotals();
    }

    /**
     * @return string
     */
    function getTotalsJson()
    {
        $totals = $this->getTotals();
        return json_encode($totals);
    }

    /**
     * @return string
     */
    function getGridOptionsJson()
    {
        $options = $this->getGridOptions();
        return json_encode($options);
    }

    /**
     * @return bool
     */
    function isTotalsEnabled()
    {
        return Mage::helper('iwd_ordergrid')->isEnabled()
            && Mage::getStoreConfig(IWD_OrderGrid_Helper_Data::CONFIG_XPATH_ORDER_GRID_TOTALS)
            && Mage::getSingleton('admin/session')->isAllowed('iwd_ordergrid/order_totals');
    }
}
