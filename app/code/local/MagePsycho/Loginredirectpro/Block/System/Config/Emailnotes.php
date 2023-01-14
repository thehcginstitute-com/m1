<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_System_Config_Emailnotes extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		return 'Go to <a href=" ' . Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/customer') . ' " title="Default Email Settings" target="_blank">System > Configuration > Customers > Customer Configuration > Create New Account Options > Default Welcome Email</a> section for customization.';
	}
}