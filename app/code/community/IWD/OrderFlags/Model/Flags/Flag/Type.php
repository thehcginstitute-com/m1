<?php

/**
 * Class IWD_OrderFlags_Model_Flags_Flag_Type
 *
 * @method string getFlagId()
 * @method IWD_OrderFlags_Model_Flags_Flag_Type setFlagId(string $value)
 * @method string getTypeId()
 * @method IWD_OrderFlags_Model_Flags_Flag_Type setTypeId(string $value)
 */
class IWD_OrderFlags_Model_Flags_Flag_Type extends Mage_Core_Model_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_flag_type');
    }
}
