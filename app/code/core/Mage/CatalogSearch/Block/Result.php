<?php
/**
 * @method $this setResultCount(int $value)
 */
class Mage_CatalogSearch_Block_Result extends Mage_Core_Block_Template
{
	/**
	 * Catalog Product collection
	 *
	 * @var Mage_CatalogSearch_Model_Resource_Fulltext_Collection|Mage_Eav_Model_Entity_Collection_Abstract|null
	 */
	protected $_productCollection;

	/**
	 * Retrieve query model object
	 *
	 * @return Mage_CatalogSearch_Model_Query
	 */
	protected function _getQuery()
	{
		/** @var Mage_CatalogSearch_Helper_Data $helper */
		$helper = $this->helper('catalogsearch');
		return $helper->getQuery();
	}

	/**
	 * Prepare layout
	 *
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		/** @var Mage_CatalogSearch_Helper_Data $helper */
		$helper = $this->helper('catalogsearch');

		// add Home breadcrumb
		/** @var Mage_Page_Block_Html_Breadcrumbs $breadcrumbs */
		$breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
		if ($breadcrumbs) {
			$title = $this->__("Search results for: '%s'", $helper->getQueryText());

			$breadcrumbs->addCrumb('home', [
				'label' => $this->__('Home'),
				'title' => $this->__('Go to Home Page'),
				'link'  => Mage::getBaseUrl()
			])->addCrumb('search', [
				'label' => $title,
				'title' => $title
			]);
		}

		// modify page title
		$title = $this->__("Search results for: '%s'", $helper->getEscapedQueryText());
		$this->getLayout()->getBlock('head')->setTitle($title);

		return parent::_prepareLayout();
	}

	/**
	 * Retrieve additional blocks html
	 *
	 * @return string
	 */
	function getAdditionalHtml()
	{
		return $this->getLayout()->getBlock('search_result_list')->getChildHtml('additional');
	}

	/**
	 * Retrieve search list toolbar block
	 *
	 * @return Mage_Catalog_Block_Product_List
	 */
	function getListBlock()
	{
		return $this->getChild('search_result_list');
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17/app/design/frontend/default/mobileshoppe/layout/catalogsearch.xml#L60
	 */
	final function setListOrders():void {
		$category = Mage::getSingleton('catalog/layer')->getCurrentCategory(); /** @var Mage_Catalog_Model_Category $category */
		$availableOrders = $category->getAvailableSortByOptions();
		unset($availableOrders['position']);
		$availableOrders = array_merge(['relevance' => $this->__('Relevance')], $availableOrders);
		$this->getListBlock()
			->setAvailableOrders($availableOrders)
			->setDefaultDirection('desc')
			->setSortBy('relevance');
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17/app/design/frontend/default/mobileshoppe/layout/catalogsearch.xml#L61
	 */
	final function setListModes():void {$this->getListBlock()->setModes(['grid' => 'Grid', 'list' => 'List']);}

	/**
	 * Retrieve Search result list HTML output
	 *
	 * @return string
	 */
	function getProductListHtml()
	{
		return $this->getChildHtml('search_result_list');
	}

	/**
	 * Retrieve loaded category collection
	 *
	 * @return Mage_CatalogSearch_Model_Resource_Fulltext_Collection|Mage_Eav_Model_Entity_Collection_Abstract
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			$this->_productCollection = $this->getListBlock()->getLoadedProductCollection();
		}

		return $this->_productCollection;
	}

	/**
	 * Retrieve search result count
	 *
	 * @return string
	 */
	function getResultCount()
	{
		if (!$this->getData('result_count')) {
			$size = $this->_getProductCollection()->getSize();
			$this->_getQuery()->setNumResults($size);
			$this->setResultCount($size);
		}
		return $this->getData('result_count');
	}

	/**
	 * Retrieve No Result or Minimum query length Text
	 *
	 * @return string
	 */
	function getNoResultText()
	{
		if (Mage::helper('catalogsearch')->isMinQueryLength()) {
			return Mage::helper('catalogsearch')->__(
				'Minimum Search query length is %s',
				$this->_getQuery()->getMinQueryLength()
			);
		}
		return $this->_getData('no_result_text');
	}

	/**
	 * Retrieve Note messages
	 *
	 * @return array
	 */
	function getNoteMessages()
	{
		return Mage::helper('catalogsearch')->getNoteMessages();
	}
}
