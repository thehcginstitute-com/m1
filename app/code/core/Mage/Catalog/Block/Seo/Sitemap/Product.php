<?php
/**
 * @method $this setCollection(Mage_Catalog_Model_Resource_Product_Collection $value)
 */
class Mage_Catalog_Block_Seo_Sitemap_Product extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{
	/**
	 * Initialize products collection
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		/** @var Mage_Catalog_Model_Resource_Product_Collection $collection */
		$collection = Mage::getModel('catalog/product')->getCollection();
		$collection
			->addAttributeToSelect('name')
			->addAttributeToSelect('url_key')
			->addStoreFilter()
			->addAttributeToFilter('status', [
				'in' => Mage::getSingleton('catalog/product_status')->getVisibleStatusIds()
			]);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

		$this->setCollection($collection);
		return $this;
	}

	/**
	 * Get item URL
	 *
	 * @param Mage_Catalog_Model_Product $product
	 * @return string
	 */
	function getItemUrl($product)
	{
		$helper = Mage::helper('catalog/product');
		/** @var Mage_Catalog_Helper_Product $helper */
		return $helper->getProductUrl($product);
	}
}
