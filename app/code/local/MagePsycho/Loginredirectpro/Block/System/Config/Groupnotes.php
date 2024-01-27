<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_System_Config_Groupnotes extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    function render(Varien_Data_Form_Element_Abstract $element)
    {
		$notes = '<div style="border: 1px solid #D6D6D6;padding:0px 4px 4px 4px;margin:0px 4px 4px 4px;background-color:white"><div style="padding:5px">
Custom Login Redirect Pro bunldes another extension called <a href="http://www.magepsycho.com/customer-group-selector-switcher.html" title="Customer Group Selector" target="_blank">Customer Group Selector</a>. It allows customers to select their required customer group at registration & checkout, using group dropdown or group code.<br /> In order to customize this feature please go to <a href="' . Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/magepsycho_customerregfields') . '" title="Manage Customer Group Selector">System > Configuration > MagePsycho Extensions > Customer Group Selector</a> section.
</div></div>';
        return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5">%s</td></tr>',
            $element->getHtmlId(), $notes
        );
    }
}