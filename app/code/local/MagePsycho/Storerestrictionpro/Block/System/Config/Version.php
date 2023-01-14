<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Block_System_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const EXTENSION_URL = 'http://www.magepsycho.com/store-restriction-pro.html';

    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf('<a href="%s" title="Store Restriction Pro" target="_blank">%s</a>', self::EXTENSION_URL, Mage::helper('magepsycho_storerestrictionpro')->getExtensionVersion());
    }
}
