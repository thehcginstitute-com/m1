<?php

/**
 * Class IWD_SettlementReport_Block_System_Config_Form_Fieldset_Extensions
 */
class IWD_SettlementReport_Block_System_Config_Form_Fieldset_Extensions
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        /**
         * @var $helper IWD_SettlementReport_Helper_Data
         */
        $helper = Mage::helper('iwd_admin_checkout');
        $available = $helper->isAvailableVersion();
        $version = Mage::getConfig()->getModuleConfig("IWD_SettlementReport")->version;

        if ($available) {
            return '<span class="notice">' . $version . '</span>';
        } else {
            return sprintf(
                '<span class="error">%s<br />%s<br />%s<br /> %s <a href="%s" target="_blank">%s</a></span>',
                $version,
                $helper->__("This module is available for Magento CE only."),
                $helper->__("You are using Enterprise version of Magento."),
                $helper->__("Please obtain Enterprise copy of the module at"),
                'https://www.iwdextensions.com',
                'https://www.iwdextensions.com'
            );
        }
    }
}