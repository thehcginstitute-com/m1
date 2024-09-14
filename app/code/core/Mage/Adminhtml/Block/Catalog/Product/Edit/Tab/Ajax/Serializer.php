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
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer extends Mage_Core_Block_Template
{
	function _construct()
	{
		parent::_construct();
		$this->setTemplate('catalog/product/edit/serializer.phtml');
		return $this;
	}

	function getProductsJSON()
	{
		$result = [];
		if ($this->getProducts()) {
			$isEntityId = $this->getIsEntityId();
			foreach ($this->getProducts() as $product) {
				$id = $isEntityId ? $product->getEntityId() : $product->getId();
				$result[$id] = $product->toArray(['qty', 'position']);
			}
		}
		return $result ? Zend_Json::encode($result) : '{}';
	}

	/**
	 * Initialize grid block under the "Related Products", "Up-sells", "Cross-sells" sections
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @param string $blockName
	 * @param string $getProductFunction
	 * @param string $inputName
	 */
	final function initSerializerBlock($blockName, $getProductFunction, $inputName):void {
		if ($block = $this->getLayout()->getBlock($blockName)) {
			$this->setGridBlock($block)
				->setProducts(Mage::registry('current_product')->$getProductFunction())
				->setInputElementName($inputName);
		}
	}
}
