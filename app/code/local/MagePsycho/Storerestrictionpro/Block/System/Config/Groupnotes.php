<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Block_System_Config_Groupnotes extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $isGroupSelectorModuleActive = Mage::getConfig()->getModuleConfig('MagePsycho_Customerregfields')->is('active', 'true');
        if (!$isGroupSelectorModuleActive) {
            $notes = '<div style="border: 1px solid #D6D6D6;padding:0px 4px 4px 4px;margin:0px 4px 4px 4px;background-color:white"><div style="padding:5px">\'Customer Group Selector\' extension is disabled. Please enable it in order to allow customer group selection during registration.</div></div>';
        } else {
            $notes = "<div style='border: 1px solid #D6D6D6;padding:0px 4px 4px 4px;margin:0px 4px 4px 4px;background-color:white'><div style='padding:5px'>
			'Store Restriction Pro' includes an extra feature for selecting customer group during New Account Creation.<br /> In order to customize this feature please go to <a href='" . Mage::helper('adminhtml')->getUrl('adminhtml/system_config/edit/section/magepsycho_customerregfields') . "' title='Manage Customer Group Selection'>System > Configuration > MagePsycho Extensions > Customer Group Selector</a> section.
</div></div>";
        }

        return sprintf('<tr class="system-fieldset-sub-head" id="row_%s"><td colspan="5">%s</td></tr>',
            $element->getHtmlId(), $notes
        );
    }
}