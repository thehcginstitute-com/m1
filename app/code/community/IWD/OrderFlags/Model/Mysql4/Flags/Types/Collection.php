<?php

/**
 * Class IWD_OrderFlags_Model_Mysql4_Flags_Types_Collection
 */
class IWD_OrderFlags_Model_Mysql4_Flags_Types_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * {@inheritdoc}
     */
    function _construct()
    {
        parent::_construct();
        $this->_init('iwd_orderflags/flags_types');
    }
}
