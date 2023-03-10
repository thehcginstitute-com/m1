<?php

class IWD_MultiInventory_Block_Adminhtml_Cataloginventory_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'iwd_multiinventory';
        $this->_controller = 'adminhtml_cataloginventory_product';

        $this->_removeButton('add');

        $this->_addButton('update', array(
            'label'   => Mage::helper('iwd_multiinventory')->__('Update'),
            'onclick' => 'cataloginventoryProductGridUpdate()',
            'class'   => 'update',
        ));

        $this->setTemplate('iwd/multiinventory/cataloginventory/product.phtml');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        if (!Mage::app()->isSingleStoreMode()) {
            return false;
        }
        return true;
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

}