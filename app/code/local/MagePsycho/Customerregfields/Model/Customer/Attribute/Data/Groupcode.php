<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Model_Customer_Attribute_Data_Groupcode extends Mage_Eav_Model_Attribute_Data_Text
{

	/**
	 * Validate only if non-empty Group Code has been entered.
	 *
	 * @param array|string $value
	 *
	 * @return array|bool
	 */
	function validateValue($value)
	{
		$helper     = Mage::helper('magepsycho_customerregfields');
		if ( ! $helper->skipGroupCodeSelectorFxn() && ! empty($value) && ! $helper->checkIfGroupCodeIsValid($value)) {
			return array(
				$helper->getConfig()->getGroupCodeErrorMessage()
			);
		}
		return  true;
	}
}