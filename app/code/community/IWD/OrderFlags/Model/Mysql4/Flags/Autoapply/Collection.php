<?php

/**
 * Class IWD_OrderFlags_Model_Mysql4_Flags_Autoapply_Collection
 */
class IWD_OrderFlags_Model_Mysql4_Flags_Autoapply_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * {@inheritdoc}
     */
    function _construct()
    {
        parent::_construct();
        $this->_init('iwd_orderflags/flags_autoapply');
    }
}
