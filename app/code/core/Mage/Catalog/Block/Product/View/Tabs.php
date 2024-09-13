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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product information tabs
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_View_Tabs extends Mage_Core_Block_Template
{
	protected $_tabs = [];

	/**
	 * Add tab to the container
     * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @return false|void
	 */
	final function addTab(string $alias, string $title, string $block, string $template) {
		if (!$title || !$block || !$template) {
			return false;
		}
		$this->_tabs[] = [
			'alias' => $alias,
			'title' => $title
		];
		$this->setChild(
			$alias,
			$this->getLayout()->createBlock($block, $alias)
				->setTemplate($template)
		);
	}

	/**
	 * @return array
	 */
	function getTabs()
	{
		return $this->_tabs;
	}
}
