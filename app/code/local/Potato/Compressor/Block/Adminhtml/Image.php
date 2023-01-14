<?php

class Potato_Compressor_Block_Adminhtml_Image extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_image';
        $this->_blockGroup = 'po_compressor';
        $this->_headerText = $this->__('Images That Require Optimization');
        parent::__construct();
        $this->removeButton('add');
    }
}