<?php
/**
 * @method $this setCollection(array|Mage_Catalog_Model_Resource_Category_Collection|Varien_Data_Collection|Varien_Data_Tree_Node_Collection $value)
 */
class Mage_Catalog_Block_Seo_Sitemap_Category extends Mage_Catalog_Block_Seo_Sitemap_Abstract
{
	/**
	 * Initialize categories collection
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		$helper = Mage::helper('catalog/category');
		/** @var Mage_Catalog_Helper_Category $helper */
		$collection = $helper->getStoreCategories('name', true, false);
		$this->setCollection($collection);
		return $this;
	}

	/**
	 * Get item URL
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	function getItemUrl($category)
	{
		$helper = Mage::helper('catalog/category');
		/** @var Mage_Catalog_Helper_Category $helper */
		return $helper->getCategoryUrl($category);
	}
}
