<?php
/**
 * @method setResultCount(int $value)
 */
class Mage_CatalogSearch_Block_Advanced_Result extends Mage_Core_Block_Template {
	/**
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		/** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		if ($breadcrumbs) {
			$breadcrumbs->addCrumb('home', [
				'label' => Mage::helper('catalogsearch')->__('Home'),
				'title' => Mage::helper('catalogsearch')->__('Go to Home Page'),
				'link' => Mage::getBaseUrl()
			])->addCrumb('search', [
				'label' => Mage::helper('catalogsearch')->__('Catalog Advanced Search'),
				'link' => $this->getUrl('*/*/')
			])->addCrumb('search_result', [
				'label' => Mage::helper('catalogsearch')->__('Results')
			]);
		}
		return parent::_prepareLayout();
	}

	function setListOrders()
	{
		$category = Mage::getSingleton('catalog/layer')
			->getCurrentCategory();
		/** @var Mage_Catalog_Model_Category $category */

		$availableOrders = $category->getAvailableSortByOptions();
		unset($availableOrders['position']);
		$availableOrders = array_merge([
			'relevance' => $this->__('Relevance')
		], $availableOrders);
		$this->getChild('search_result_list')
			->setAvailableOrders($availableOrders)
			->setSortBy('relevance');
	}

	function setListModes()
	{
		$this->getChild('search_result_list')
			->setModes([
				'grid' => Mage::helper('catalogsearch')->__('Grid'),
				'list' => Mage::helper('catalogsearch')->__('List')]);
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	function setListCollection():void {$this->getChild('search_result_list')->setCollection($this->_getProductCollection());}

	/**
	 * @return Mage_CatalogSearch_Model_Resource_Advanced_Collection
	 */
	protected function _getProductCollection()
	{
		return $this->getSearchModel()->getProductCollection();
	}

	/**
	 * @return Mage_CatalogSearch_Model_Advanced|Mage_Core_Model_Abstract
	 */
	function getSearchModel()
	{
		return Mage::getSingleton('catalogsearch/advanced');
	}

	/**
	 * @return int
	 */
	function getResultCount()
	{
		if (!$this->getData('result_count')) {
			$size = $this->getSearchModel()->getProductCollection()->getSize();
			$this->setResultCount($size);
		}
		return $this->getData('result_count');
	}

	/**
	 * @return string
	 */
	function getProductListHtml()
	{
		return $this->getChildHtml('search_result_list');
	}

	/**
	 * @return string
	 * @throws Exception
	 */
	function getFormUrl()
	{
		return Mage::getModel('core/url')
			->setQueryParams($this->getRequest()->getQuery())
			->getUrl('*/*/', ['_escape' => true]);
	}

	/**
	 * @return array
	 */
	function getSearchCriterias()
	{
		$searchCriterias = $this->getSearchModel()->getSearchCriterias();
		$middle = ceil(count($searchCriterias) / 2);
		$left = array_slice($searchCriterias, 0, $middle);
		$right = array_slice($searchCriterias, $middle);

		return ['left' => $left, 'right' => $right];
	}
}
