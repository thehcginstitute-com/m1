<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Flags_Edit
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Flags_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'iwd_orderflags';
        $this->_controller = 'adminhtml_flags_flags';

        $this->_updateButton('save', 'label', Mage::helper('iwd_orderflags')->__('Save Label'));

        $this->_addButton(
            'saveandcontinue',
            array(
                'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
                'onclick' => "editForm.submit($('edit_form').action+'back/edit/')",
                'class' => 'save',
            ),
            -100,
            100
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        if ($this->isEdit()) {
            return Mage::helper('iwd_orderflags')->__("Edit Label '%s'", $this->getFormData()->getName());
        }

        return Mage::helper('iwd_orderflags')->__('Add New Label');
    }

    /**
     * @return bool
     */
    protected function isEdit()
    {
        return $this->getFormData() && $this->getFormData()->getId();
    }

    /**
     * @return mixed
     */
    protected function getFormData()
    {
        return Mage::registry('iwd_om_flags');
    }
}
