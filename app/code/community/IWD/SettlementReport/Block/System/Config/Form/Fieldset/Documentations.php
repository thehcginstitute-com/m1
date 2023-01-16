<?php

/**
 * Class IWD_SettlementReport_Block_System_Config_Form_Fieldset_Documentations
 */
class IWD_SettlementReport_Block_System_Config_Form_Fieldset_Documentations
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const USER_GUIDE_URL = "https://www.iwdagency.com/help/authorize-net-settlement-report";

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="' . self::USER_GUIDE_URL . '" target="_blank">' .
            Mage::helper('iwd_admin_checkout')->__('User Guide') .
            '</a></span>';
    }
}
