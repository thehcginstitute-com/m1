<?php

/**
 * Class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Init
 */
class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Init extends Mage_Core_Block_Template
{
    /**
     * @return bool
     */
    function isLimitPeriod()
    {
        return $this->getOrderGridModel()->isLimitPeriod();
    }

    /**
     * @return bool
     */
    function isFixHeaderEnabled()
    {
        return $this->getOrderGridModel()->isFixGridHeader();
    }

    /**
     * @return IWD_OrderGrid_Model_Order_Grid
     */
    protected function getOrderGridModel()
    {
        return Mage::getModel('iwd_ordergrid/order_grid');
    }

    /**
     * @return string
     */
    function getCreatedAtFrom()
    {
        return $this->getSession()->getData("created_at_from");
    }

    /**
     * @return string
     */
    function getCreatedAtTo()
    {
        return $this->getSession()->getData("created_at_to");
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

    /**
     * @return bool
     */
    function isTotalsEnabled()
    {
        return $this->getTotalsBlock()->isTotalsEnabled();
    }

    /**
     * @return string
     */
    function getTotalsGridOptionsJson()
    {
        return $this->getTotalsBlock()->getGridOptionsJson();
    }

    /**
     * @return string
     */
    function getTotalsJson()
    {
        return $this->getTotalsBlock()->getTotalsJson();
    }

    /**
     * @return IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Totals
     */
    protected function getTotalsBlock()
    {
        return Mage::getBlockSingleton('iwd_ordergrid/adminhtml_sales_order_grid_totals');
    }

    /**
     * @return bool
     */
    function isAllowedEditMassaction()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordergrid/manage_massaction');
    }
}
