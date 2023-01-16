<?php

/**
 * Class IWD_OrderGrid_Block_System_Config_Form_Fieldset_Documentations
 */
class IWD_OrderGrid_Block_System_Config_Form_Fieldset_Documentations extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * LInk to user guide
     */
    const USER_GUIDE_URL = "http://www.iwdagency.com/help/m1-custom-order-grid/";

    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<span class="notice"><a href="' . self::USER_GUIDE_URL . '" target="_blank">' .
                    Mage::helper('iwd_ordergrid')->__('User Guide') .
                '</a></span>' .
                $this->conflicts();
    }

    /**
     * @return string
     */
    protected function conflicts()
    {
        $conflicts = "";

        /**
         * @var $orderGridConflicts IWD_OrderGrid_Model_Conflicts
         */
        $orderGridConflicts = Mage::getModel('iwd_ordergrid/conflicts');

        $rewrites = $orderGridConflicts->getRewritesClasses();
        foreach ($rewrites as $base => $classes) {
            $conflicts .= '<li>' . $base . '<ul style="margin-left:40px;">';
            foreach ($classes as $class) {
                $conflicts .= '<li>' . $class . '</li>';
            }

            $conflicts .= "</ul></li>";
        }

        if (!empty($conflicts)) {
            $conflicts = '<div style="border:1px dotted red;margin:5px 0;padding:0 5px;">'
                . '<p class="error">WARNING! Extension has conflicts:</p>'
                . $conflicts
                . '</div><p class="note error"><span>Please, resolve conflicts for correct work.</span></p>';
        }

        return $conflicts;
    }
}
