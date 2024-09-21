<?php
/**
 * Block, that can get data from layout or from registry.
 * Can compare its data values by specified keys
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Block_Template_Facade extends Mage_Core_Block_Template {
	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setDataByKey(string $k, string $v):void {$this->_data[$k] = $v;}

	/**
	 * Also set data, but take the value from registry by registry key
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setDataByKeyFromRegistry(string $k, string $rk):void {
		if ($o = Mage::registry($rk)) {
			$this->setDataByKey($k, $o[$k]);
		}
	}

	/**
	 * Check if data values by specified keys are equal
	 * $conditionKeys can be array or arbitrary set of params (func_get_args())
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by Mage_Core_Block_Abstract::unsetCallChild()
	 */
	function ifEquals(string ...$conditionKeys):bool {
		if (!empty($conditionKeys)) {
			foreach ($conditionKeys as $key) {
				if (!isset($this->_data[$key])) {
					return false;
				}
			}
			$lastValue = $this->_data[$key];
			foreach ($conditionKeys as $key) {
				if ($this->_data[$key] !== $lastValue) {
					return false;
				}
			}
		}
		return true;
	}
}