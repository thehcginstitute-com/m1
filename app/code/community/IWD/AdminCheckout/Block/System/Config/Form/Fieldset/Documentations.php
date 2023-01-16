<?php

/**
 * Class IWD_AdminCheckout_Block_System_Config_Form_Fieldset_Documentations
 */
class IWD_AdminCheckout_Block_System_Config_Form_Fieldset_Documentations
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const USER_GUIDE_URL = "https://www.iwdagency.com/help/order-creation-pro/";

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="' . self::USER_GUIDE_URL . '" target="_blank">' .
                    Mage::helper('iwd_admin_checkout')->__('User Guide') .
                '</a></span>' .
                $this->conflicts();
    }

    /**
     * @return string
     */
    protected function conflicts()
    {
        $conflicts = "";

        $rewrites = Mage::getModel('iwd_admin_checkout/conflicts')->getRewritesClasses();
        foreach ($rewrites as $base => $classes) {
            $conflicts .= '<li>' . $base . '<ul style="margin-left:40px;">';
            foreach ($classes as $class) {
                $conflicts .= '<li>' . $class . '</li>';
            }

            $conflicts .= "</ul></li>";
        }

        if (empty($conflicts)) {
            return "";
        }

        return '<div style="border:1px dotted red;margin:5px 0;padding:0 5px;">'
            . '<p class="error">WARNING! Extension has conflicts:</p>'
            . $conflicts
            . '</div><p class="note error"><span>Please, resolve conflicts for correct work.</span></p>';
    }
}
