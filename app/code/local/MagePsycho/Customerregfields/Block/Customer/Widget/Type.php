<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_Customer_Widget_Type extends MagePsycho_Customerregfields_Block_Customer_Widget_Abstract
{
	function _toHtml()
	{
		if ($this->helper('magepsycho_customerregfields')->isFxnSkipped()) {
			return '';
		}
		$groupSelectorType  = $this->getConfig()->getGroupSelectionType();

		//@todo Make setMethod workable directly from .phtml
		if ($groupSelectorType == MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_GROUP_CODE) {
			return $this->getLayout()
					->createBlock('magepsycho_customerregfields/customer_widget_type_groupcode')
					->setObject($this->getObject())
					->setIsEditPage($this->getIsEditPage())
					->setFieldIdFormat($this->getFieldIdFormat())
					->setFieldNameFormat($this->getFieldNameFormat())
					->toHtml();
		} else if ($groupSelectorType == MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_DROPDOWN) {
			return $this->getLayout()
					->createBlock('magepsycho_customerregfields/customer_widget_type_groupid')
					->setObject($this->getObject())
					->setIsEditPage($this->getIsEditPage())
					->setFieldIdFormat($this->getFieldIdFormat())
					->setFieldNameFormat($this->getFieldNameFormat())
					->toHtml();
		}
		return '';
	}

}