<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Form
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
                'method' => 'post',
                'enctype' => 'multipart/form-data',
            )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
