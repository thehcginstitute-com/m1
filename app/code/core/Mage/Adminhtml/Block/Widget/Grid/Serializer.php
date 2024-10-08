<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Grid_Serializer extends Mage_Core_Block_Template
{
	/**
	 * Store grid input names to serialize
	 *
	 * @var array
	 */
	private $_inputsToSerialize = [];

	/**
	 * Set serializer template
	 *
	 * @return $this
	 */
	function _construct()
	{
		parent::_construct();
		$this->setTemplate('widget/grid/serializer.phtml');
		return $this;
	}

	/**
	 * Register grid column input name to serialize
	 *
	 * @param array|string $names
	 */
	function addColumnInputName($names)
	{
		if (is_array($names)) {
			foreach ($names as $name) {
				$this->addColumnInputName($name);
			}
		} else {
			if (!in_array($names, $this->_inputsToSerialize)) {
				$this->_inputsToSerialize[] = $names;
			}
		}
	}

	/**
	 * Get grid column input names to serialize
	 *
	 * @param bool $asJSON
	 * @return array|string
	 */
	function getColumnInputNames($asJSON = false)
	{
		if ($asJSON) {
			return Mage::helper('core')->jsonEncode($this->_inputsToSerialize);
		}
		return $this->_inputsToSerialize;
	}

	/**
	 * Get object data as JSON
	 *
	 * @return string
	 */
	function getDataAsJSON()
	{
		$result = [];
		if ($serializeData = $this->getSerializeData()) {
			$result = $serializeData;
		} elseif (!empty($this->_inputsToSerialize)) {
			return '{}';
		}
		return Mage::helper('core')->jsonEncode($result);
	}

	/**
	 * Initialize grid block
	 * Get grid block from layout by specified block name
	 * Get serialize data to manage it (called specified method, that return data to manage)
	 * Also use reload param name for saving grid checked boxes states
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @param Mage_Adminhtml_Block_Widget_Grid | string $grid grid object or grid block name
	 */
	final function initSerializerBlock(
		$grid, string $callback, string $hiddenInputName, string $reloadParamName = 'entityCollection'
	):void {
		if (is_string($grid)) {
			$grid = $this->getLayout()->getBlock($grid);
		}
		if ($grid instanceof Mage_Adminhtml_Block_Widget_Grid) {
			$this->setGridBlock($grid)
				 ->setInputElementName($hiddenInputName)
				 ->setReloadParamName($reloadParamName)
				 ->setSerializeData($grid->$callback());
		}
	}
}
