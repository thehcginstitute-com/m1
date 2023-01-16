<?php

/**
 * Class IWD_OrderFlags_Block_System_Config_Form_Fieldset_Documentations
 */
class IWD_OrderFlags_Block_System_Config_Form_Fieldset_Documentations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const USER_GUIDE_URL = "https://www.iwdagency.com/help/m1-order-labels/";

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="' . self::USER_GUIDE_URL . '" target="_blank">' .
                    Mage::helper('iwd_orderflags')->__('User Guide') .
                '</a></span>';
    }
}
