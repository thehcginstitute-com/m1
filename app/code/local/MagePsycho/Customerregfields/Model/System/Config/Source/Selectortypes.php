<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes
{
	const SELECTOR_TYPE_DROPDOWN    = 1;
	const SELECTOR_TYPE_GROUP_CODE  = 2;

	protected $_options;

	function getAllOptions($withEmpty = false)
	{
		if (is_null($this->_options)) {
			$this->_options = array(
				array(
					'value' => self::SELECTOR_TYPE_DROPDOWN,
					'label' => Mage::helper('magepsycho_customerregfields')->__('Dropdown'),
				),

				array(
					'value' => self::SELECTOR_TYPE_GROUP_CODE,
					'label' => Mage::helper('magepsycho_customerregfields')->__('Group Code'),
				),
			);

		}
		$options = $this->_options;
		if ($withEmpty) {
			array_unshift($options, array('value' => '', 'label' => ''));
		}
		return $options;
	}

	function getOptionsArray($withEmpty = true)
	{
		$options = array();
		foreach ($this->getAllOptions($withEmpty) as $option) {
			$options[$option['value']] = $option['label'];
		}
		return $options;
	}

	function getOptionText($value)
	{
		$options = $this->getAllOptions(false);
		foreach ($options as $item) {
			if ($item['value'] == $value) {
				return $item['label'];
			}
		}
		return false;
	}

	function toOptionArray()
	{
		return $this->getAllOptions();
	}
}