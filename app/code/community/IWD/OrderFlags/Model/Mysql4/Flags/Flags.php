<?php

/**
 * Class IWD_OrderFlags_Model_Mysql4_Flags_Flags
 */
class IWD_OrderFlags_Model_Mysql4_Flags_Flags extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_flags', 'id');
    }
}
