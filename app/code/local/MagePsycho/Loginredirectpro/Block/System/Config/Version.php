<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_System_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const EXTENSION_URL = 'http://www.magepsycho.com/custom-login-redirect-pro.html';

	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return '<a href=" ' . self::EXTENSION_URL . ' " title="Custom Login Redirect Pro" target="_blank">' . Mage::helper('magepsycho_loginredirectpro')->getExtensionVersion() . '</a>';
    }
}