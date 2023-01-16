<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Types
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Types extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->_blockGroup = 'iwd_orderflags';
        $this->_controller = 'adminhtml_flags_types';
        $this->_headerText = Mage::helper('iwd_orderflags')->__('Manage Columns for Labels');
        $this->_addButtonLabel = $this->__('Add New Column');

        $this->_addButton(
            'flags',
            array(
                'label'   => $this->__('Manage Labels'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/flags_flags/index') .'\')',
                'class'   => 'go',
            )
        );

        parent::__construct();
    }
}
