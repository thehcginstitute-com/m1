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
 * Site Map category block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Catalog_Block_Seo_Sitemap_Abstract extends Mage_Core_Block_Template
{
	/**
	 * Init pager
	 * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @see \Mage_Catalog_Block_Seo_Sitemap_Tree_Category::bindPager()
	 */
	function bindPager(string $pagerName):void {
		$pager = $this->getLayout()->getBlock($pagerName);
		/** @var Mage_Page_Block_Html_Pager $pager */
		if ($pager) {
			$pager->setAvailableLimit([50 => 50]);
			$pager->setCollection($this->getCollection());
			$pager->setShowPerPage(false);
		}
	}

	/**
	 * Get item URL
	 *
	 * In most cases should be overridden in descendant blocks
	 *
	 * @param Mage_Catalog_Block_Seo_Sitemap_Abstract $item
	 * @return string
	 */
	function getItemUrl($item)
	{
		return $item->getUrl();
	}
}
