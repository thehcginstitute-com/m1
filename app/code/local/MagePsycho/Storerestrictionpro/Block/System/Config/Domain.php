<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Block_System_Config_Domain extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		return Mage::helper('magepsycho_storerestrictionpro')->getDomainFromSystemConfig();
	}
}
