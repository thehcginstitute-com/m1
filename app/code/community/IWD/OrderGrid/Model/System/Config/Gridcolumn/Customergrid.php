<?php

/**
 * Class IWD_OrderGrid_Model_System_Config_Gridcolumn_Customergrid
 */
class IWD_OrderGrid_Model_System_Config_Gridcolumn_Customergrid extends IWD_OrderGrid_Model_System_Config_Gridcolumn_Order
{
    protected function getSelectedColumnsArray()
    {
        return Mage::getModel('iwd_ordergrid/order_grid')
            ->getSelectedColumnsArray(IWD_OrderGrid_Model_Customer_Order::XML_PATH_CUSTOMER_ORDERS_ORDER_GRID_COLUMN);
    }

    protected function allowDispatchEvent()
    {
        return false;
    }
}
