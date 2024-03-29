<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_System_Config_Domain extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		return hcg_mp_hc()->getDomainFromSystemConfig();
	}
}
