<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Tabs
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        parent::__construct();
        $this->setId('iwd_orderflags_flag_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('iwd_orderflags')->__('Column Information'));
    }

    /**
     * {@inheritdoc}
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'form_tab_flag_general',
            array(
                'label' => Mage::helper('iwd_orderflags')->__('General Information'),
                'title' => Mage::helper('iwd_orderflags')->__('General Information'),
                'content' => $this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_types_edit_tab_general')->toHtml(),
            )
        );

        $this->addTab(
            'form_tab_flag_flags',
            array(
                'label' => Mage::helper('iwd_orderflags')->__('Assign Labels'),
                'title' => Mage::helper('iwd_orderflags')->__('Assign Labels'),
                'content' => $this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_types_edit_tab_flags')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}
