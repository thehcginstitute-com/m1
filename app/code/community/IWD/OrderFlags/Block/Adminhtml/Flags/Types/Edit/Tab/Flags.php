<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Tab_Flags
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Tab_Flags extends Mage_Adminhtml_Block_Widget_Form
{
    protected $form;

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        try {
            $this->form = new Varien_Data_Form();
            $this->setForm($this->form);

            $this->addFieldsetToForm();
            $this->setValuesToForm();
        } catch (Exception $e) {
            Mage::helper('iwd_orderflags')->addExceptionToLog($e, __CLASS__);
        }

        return parent::_prepareForm();
    }

    protected function addFieldsetToForm()
    {
        $helper = Mage::helper('iwd_orderflags');

        $fieldset = $this->form->addFieldset(
            'iwd_om_flags_types_flags',
            array('legend' => $helper->__('Assigned Labels'))
        );

        $fieldset->addField(
            'flags',
            'multiselect',
            array(
                'label' => $helper->__('Assign Labels to Column'),
                'title' => $helper->__('Assign Labels to Column'),
                'name' => 'flags',
                'values' => Mage::getModel('iwd_orderflags/flags_flags')->toOptionArray(),
                'after_element_html' => '<p class="note">Apply selected label(s) to column, or <a href="'
                    . $this->getUrl('*/flags_flags/new')  . '" target="_blank">create new label</a></p>',
            )
        );
    }

    protected function setValuesToForm()
    {
        $types = $this->getTypesData();
        $data = ($types && !is_array($types)) ? $types->getData() : array();
        $this->form->setValues($data);
    }

    protected function getTypesData()
    {
        return Mage::registry('iwd_om_flags_types');
    }
}
