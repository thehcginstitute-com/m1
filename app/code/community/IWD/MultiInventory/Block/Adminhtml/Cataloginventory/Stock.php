<?php

class IWD_MultiInventory_Block_Adminhtml_Cataloginventory_Stock extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'iwd_multiinventory';
        $this->_controller = 'adminhtml_cataloginventory_stock';
        $this->_headerText = Mage::helper('iwd_multiinventory')->__('Sources');

        parent::__construct();
    }
}