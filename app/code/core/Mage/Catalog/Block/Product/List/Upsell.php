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
 * Catalog product related items block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_List_Upsell extends Mage_Catalog_Block_Product_Abstract
{
	/**
	 * Default MAP renderer type
	 *
	 * @var string
	 */
	protected $_mapRenderer = 'msrp_noform';

	protected $_columnCount = 4;

	protected $_items;

	protected $_itemCollection;

	protected $_itemLimits = [];

	/**
	 * @return $this
	 */
	protected function _prepareData()
	{
		$product = Mage::registry('product');
		/** @var Mage_Catalog_Model_Product $product */
		$this->_itemCollection = $product->getUpSellProductCollection()
			->setPositionOrder()
			->addStoreFilter()
		;
		if (Mage::helper('catalog')->isModuleEnabled('Mage_Checkout')) {
			Mage::getResourceSingleton('checkout/cart')->addExcludeProductFilter(
				$this->_itemCollection,
				Mage::getSingleton('checkout/session')->getQuoteId()
			);

			$this->_addProductAttributesAndPrices($this->_itemCollection);
		}
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_itemCollection);

		if ($this->getItemLimit('upsell') > 0) {
			$this->_itemCollection->setPageSize($this->getItemLimit('upsell'));
		}

		$this->_itemCollection->load();

		/**
		 * Updating collection with desired items
		 */
		Mage::dispatchEvent('catalog_product_upsell', [
			'product'       => $product,
			'collection'    => $this->_itemCollection,
			'limit'         => $this->getItemLimit()
		]);

		foreach ($this->_itemCollection as $product) {
			$product->setDoNotUseCategoryId(true);
		}

		return $this;
	}

	/**
	 * @return Mage_Catalog_Block_Product_Abstract
	 */
	protected function _beforeToHtml()
	{
		$this->_prepareData();
		return parent::_beforeToHtml();
	}

	/**
	 * @return Mage_Catalog_Model_Resource_Product_Link_Product_Collection
	 */
	function getItemCollection()
	{
		return $this->_itemCollection;
	}

	/**
	 * @return Mage_Catalog_Model_Product[]
	 */
	function getItems()
	{
		if (is_null($this->_items) && $this->getItemCollection()) {
			$this->_items = $this->getItemCollection()->getItems();
		}
		return $this->_items;
	}

	/**
	 * @return float
	 */
	function getRowCount()
	{
		return ceil(count($this->getItemCollection()->getItems()) / $this->getColumnCount());
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @override
	 * @see Mage_Catalog_Block_Product_Abstract::setColumnCount()
	 */
	final function setColumnCount(int $v):void {$this->_columnCount = $v;}

	/**
	 * @return int
	 */
	function getColumnCount()
	{
		return $this->_columnCount;
	}

	function resetItemsIterator()
	{
		$this->getItems();
		reset($this->_items);
	}

	/**
	 * @return mixed
	 */
	function getIterableItem()
	{
		$item = current($this->_items);
		next($this->_items);
		return $item;
	}

	/**
	 * Set how many items we need to show in upsell block
	 * Notice: this parametr will be also applied
     * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--11/app/design/frontend/base/default/layout/bundle.xml#L102
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--11/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L263
	 */
	final function setItemLimit(string $t, int $v):void {$this->_itemLimits[$t] = $v;}

	/**
	 * @param string $type
	 * @return array|int|mixed
	 */
	function getItemLimit($type = '')
	{
		if ($type == '') {
			return $this->_itemLimits;
		}
		return $this->_itemLimits[$type] ?? 0;
	}

	/**
	 * Get tags array for saving cache
	 *
	 * @return array
	 */
	function getCacheTags()
	{
		return array_merge(parent::getCacheTags(), $this->getItemsTags($this->getItems()));
	}
}
