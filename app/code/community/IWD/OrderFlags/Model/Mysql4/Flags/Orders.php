<?php

/**
 * Class IWD_OrderFlags_Model_Mysql4_Flags_Orders
 */
class IWD_OrderFlags_Model_Mysql4_Flags_Orders extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_orders', 'id');
    }
}
