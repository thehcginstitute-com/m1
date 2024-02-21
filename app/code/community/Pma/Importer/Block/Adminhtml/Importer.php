<?php
 
    class Pma_Importer_Block_Adminhtml_Importer extends Mage_Adminhtml_Block_Widget_Grid_Container
    {
         function __construct()
        {  
            $this->_controller = 'adminhtml_importer';
            $this->_blockGroup = 'importer';
            $this->_headerText = Mage::helper('importer')->__('Order Manager');
            parent::__construct();
            $this->_removeButton('add');
        }
    }