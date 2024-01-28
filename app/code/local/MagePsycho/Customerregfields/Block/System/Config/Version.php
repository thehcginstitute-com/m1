<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_System_Config_Version extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    const EXTENSION_URL = 'http://www.magepsycho.com/customer-group-selector-switcher.html';
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return sprintf('<a href="%s" target="_blank" title="Customer Group Selector">%s</a>', self::EXTENSION_URL, hcg_mp_hc()->getExtensionVersion());
    }
}