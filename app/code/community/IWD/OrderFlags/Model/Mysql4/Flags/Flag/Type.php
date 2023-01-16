<?php

/**
 * Class IWD_OrderFlags_Model_Mysql4_Flags_Flag_Type
 */
class IWD_OrderFlags_Model_Mysql4_Flags_Flag_Type extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_flag_type', 'id');
    }
}
