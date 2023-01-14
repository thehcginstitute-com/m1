<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_System_Config_License extends Mage_Adminhtml_Block_System_Config_Form_Field
{
	protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$html = $element->getElementHtml();
		if (strlen($element->getEscapedValue()) < 32) {
			$icon = Mage::getDesign()->getSkinUrl('images/error_msg_icon.gif');
		} else {
			$icon = Mage::getDesign()->getSkinUrl('images/success_msg_icon.gif');
		}
		$html .= '<span style="display: inline-block;"><img src="' . $icon . '" style="border: 0; margin: 0; vertical-align: text-bottom;"></span>';
		$html .= '<script type="text/javascript">
Event.observe(window, "load", function() {
    $("' . $element->getHtmlId() . '").setStyle({width: "256px"})
});
</script>';
		return $html;
	}
}
