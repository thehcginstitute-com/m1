<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog Product Price Template Block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Price_Template extends Mage_Core_Block_Abstract
{
	/**
	 * Product Price block types cache
	 *
	 * @var array
	 */
	protected $_priceBlockTypes = [];

	/**
	 * Retrieve array of Price Block Types
	 *
	 * Key is price block type name and value is array of
	 * template and block strings
	 *
	 * @return array
	 */
	function getPriceBlockTypes()
	{
		return $this->_priceBlockTypes;
	}

	/**
	 * Adding customized price template for product type
     * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function addPriceBlockType(string $type, string $block = '', string $template = ''):self {
		if ($type) {
			$this->_priceBlockTypes[$type] = [
				'block'     => $block,
				'template'  => $template
			];
		}
		return $this;
	}
}
