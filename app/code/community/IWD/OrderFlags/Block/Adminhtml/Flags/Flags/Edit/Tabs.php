<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Flags_Edit_Tabs
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Flags_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        parent::__construct();
        $this->setId('orderflags_flag_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('iwd_orderflags')->__('Label Information'));
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
                'content' => $this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_flags_edit_tab_general')->toHtml(),
            )
        );

        $this->addTab(
            'form_tab_flag_settings',
            array(
                'label' => Mage::helper('iwd_orderflags')->__('Settings'),
                'title' => Mage::helper('iwd_orderflags')->__('Settings'),
                'content' => $this->getLayout()->createBlock('iwd_orderflags/adminhtml_flags_flags_edit_tab_settings')->toHtml(),
            )
        );

        return parent::_beforeToHtml();
    }
}
