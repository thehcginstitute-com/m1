<?php

/**
 * Class IWD_MultiInventory_Model_Mysql4_Cataloginventory_Stock_Address
 */
class IWD_MultiInventory_Model_Mysql4_Cataloginventory_Stock_Address extends Mage_Core_Model_Mysql4_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_multiinventory/cataloginventory_stock_address', 'id');
    }
}
