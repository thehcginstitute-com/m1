<?php
/**
 * @method array getAvailableOrders()
 * @method $this setAvailableOrders(array $value)
 * @method int getCategoryId()
 * @method $this setCategoryId(int $value)
 * @method string getDefaultDirection()
 * @method $this setDefaultDirection(string $value)
 * @method array getModes()
 * @method $this setModes(array $value)
 * @method string getSortBy()
 * @method $this setSortBy(string $value)
 * @method bool getShowRootCategory()
 */
class Mage_Catalog_Block_Product_List extends Mage_Catalog_Block_Product_Abstract {
	/**
	 * Default toolbar block name
	 *
	 * @var string
	 */
	protected $_defaultToolbarBlock = 'catalog/product_list_toolbar';

	/**
	 * Product Collection
	 *
	 * @var Mage_Eav_Model_Entity_Collection_Abstract|null
	 */
	protected $_productCollection;

	/**
	 * Retrieve loaded category collection
	 *
	 * @return Mage_Catalog_Model_Resource_Product_Collection
	 */
	protected function _getProductCollection()
	{
		if (is_null($this->_productCollection)) {
			$layer = $this->getLayer();
			/** @var Mage_Catalog_Model_Layer $layer */
			if ($this->getShowRootCategory()) {
				$this->setCategoryId(Mage::app()->getStore()->getRootCategoryId());
			}

			if (Mage::registry('product')) {
				/** @var Mage_Catalog_Model_Resource_Category_Collection $categories */
				$categories = Mage::registry('product')->getCategoryCollection()
					->setPage(1, 1)
					->load();
				if ($categories->count()) {
					$this->setCategoryId($categories->getFirstItem()->getId());
				}
			}

			$origCategory = null;
			if ($this->getCategoryId()) {
				$category = Mage::getModel('catalog/category')->load($this->getCategoryId());
				if ($category->getId()) {
					$origCategory = $layer->getCurrentCategory();
					$layer->setCurrentCategory($category);
					$this->addModelTags($category);
				}
			}
			$this->_productCollection = $layer->getProductCollection();

			$this->prepareSortableFieldsByCategory($layer->getCurrentCategory());

			if ($origCategory) {
				$layer->setCurrentCategory($origCategory);
			}
		}

		return $this->_productCollection;
	}

	/**
	 * Get catalog layer model
	 *
	 * @return Mage_Catalog_Model_Layer
	 */
	function getLayer()
	{
		$layer = Mage::registry('current_layer');
		if ($layer) {
			return $layer;
		}
		return Mage::getSingleton('catalog/layer');
	}

	/**
	 * Retrieve loaded category collection
	 *
	 * @return Mage_Eav_Model_Entity_Collection_Abstract
	 */
	function getLoadedProductCollection()
	{
		return $this->_getProductCollection();
	}

	/**
	 * Retrieve current view mode
	 *
	 * @return string
	 */
	function getMode()
	{
		return $this->getChild('toolbar')->getCurrentMode();
	}

	/**
	 * Need use as _prepareLayout - but problem in declaring collection from
	 * another block (was problem with search result)
	 */
	protected function _beforeToHtml()
	{
		$toolbar = $this->getToolbarBlock();

		// called prepare sortable parameters
		$collection = $this->_getProductCollection();

		// use sortable parameters
		if ($orders = $this->getAvailableOrders()) {
			$toolbar->setAvailableOrders($orders);
		}
		if ($sort = $this->getSortBy()) {
			$toolbar->setDefaultOrder($sort);
		}
		if ($dir = $this->getDefaultDirection()) {
			$toolbar->setDefaultDirection($dir);
		}
		if ($modes = $this->getModes()) {
			$toolbar->setModes($modes);
		}

		// set collection to toolbar and apply sort
		$toolbar->setCollection($collection);

		$this->setChild('toolbar', $toolbar);
		Mage::dispatchEvent('catalog_block_product_list_collection', [
			'collection' => $this->_getProductCollection()
		]);

		$this->_getProductCollection()->load();

		return parent::_beforeToHtml();
	}

	/**
     * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--6/app/design/frontend/default/mobileshoppe/layout/catalogsearch.xml#L110
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--6/app/design/frontend/default/mobileshoppe/layout/catalogsearch.xml#L58
	 * @used-by https://github.com/thehcginstitute-com/m1/tree/2024-09-21--6/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L48
	 * @used-by https://github.com/thehcginstitute-com/m1/tree/2024-09-21--6/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L83
	 */
	final function setToolbarBlockName(string $v):void {$this[self::$TOOLBAR_BLOCK_NAME] = $v;}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getToolbarBlock()
	 * @used-by self::setToolbarBlockName()
	 * @const string
	 */
	private static $TOOLBAR_BLOCK_NAME = 'toolbar_block_name';

	/**
	 * Retrieve Toolbar block
	 *
	 * @return Mage_Catalog_Block_Product_List_Toolbar|Mage_Core_Block_Abstract
	 */
	function getToolbarBlock() {
		if ($blockName = $this[self::$TOOLBAR_BLOCK_NAME]) {
			if ($block = $this->getLayout()->getBlock($blockName)) {
				return $block;
			}
		}
		$block = $this->getLayout()->createBlock($this->_defaultToolbarBlock, microtime());
		return $block;
	}

	/**
	 * Retrieve additional blocks html
	 *
	 * @return string
	 */
	function getAdditionalHtml()
	{
		return $this->getChildHtml('additional');
	}

	/**
	 * Retrieve list toolbar HTML
	 *
	 * @return string
	 */
	function getToolbarHtml()
	{
		return $this->getChildHtml('toolbar');
	}

	/**
	 * @param Mage_Catalog_Model_Resource_Product_Collection $collection
	 * @return $this
	 */
	function setCollection($collection)
	{
		$this->_productCollection = $collection;
		return $this;
	}

	/**
	 * @param array|string|integer|Mage_Core_Model_Config_Element $code
	 * @return $this
	 * @throws Mage_Core_Exception
	 */
	function addAttribute($code)
	{
		$this->_getProductCollection()->addAttributeToSelect($code);
		return $this;
	}

	/**
	 * @return mixed
	 */
	function getPriceBlockTemplate()
	{
		return $this->_getData('price_block_template');
	}

	/**
	 * Retrieve Catalog Config object
	 *
	 * @return Mage_Catalog_Model_Config
	 */
	protected function _getConfig()
	{
		return Mage::getSingleton('catalog/config');
	}

	/**
	 * Prepare Sort By fields from Category Data
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return $this
	 */
	function prepareSortableFieldsByCategory($category)
	{
		if (!$this->getAvailableOrders()) {
			$this->setAvailableOrders($category->getAvailableSortByOptions());
		}
		$availableOrders = $this->getAvailableOrders();
		if (!$this->getSortBy()) {
			if ($categorySortBy = $category->getDefaultSortBy()) {
				if (!$availableOrders) {
					$availableOrders = $this->_getConfig()->getAttributeUsedForSortByArray();
				}
				if (isset($availableOrders[$categorySortBy])) {
					$this->setSortBy($categorySortBy);
				}
			}
		}

		return $this;
	}

	/**
	 * Retrieve block cache tags based on product collection
	 *
	 * @return array
	 */
	function getCacheTags()
	{
		return array_merge(
			parent::getCacheTags(),
			$this->getItemsTags($this->_getProductCollection())
		);
	}
}
