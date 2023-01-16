<?php

/**
 * Class IWD_MultiInventory_Model_Mysql4_Cataloginventory_Stock_Address_Collection
 */
class IWD_MultiInventory_Model_Mysql4_Cataloginventory_Stock_Address_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('iwd_multiinventory/cataloginventory_stock_address');
    }
}