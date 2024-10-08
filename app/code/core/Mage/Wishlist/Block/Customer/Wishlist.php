<?php
# 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Wishlist_Block_Customer_Wishlist extends Mage_Wishlist_Block_Abstract
{
	/**
	 * List of product options rendering configurations by product type
	 */
	protected $_optionsCfg = [];

	/**
	 * Add wishlist conditions to collection
	 *
	 * @param  Mage_Wishlist_Model_Resource_Item_Collection $collection
	 * @return $this
	 */
	protected function _prepareCollection($collection)
	{
		$collection->setInStockFilter(true)->setOrder('added_at', 'ASC');
		return $this;
	}

	/**
	 * Preparing global layout
	 *
	 * @return $this
	 */
	protected function _prepareLayout()
	{
		parent::_prepareLayout();
		$headBlock = $this->getLayout()->getBlock('head');
		if ($headBlock) {
			$headBlock->setTitle($this->__('My Wishlist'));
		}
		return $this;
	}

	/**
	 * Retrieve Back URL
	 *
	 * @return string
	 */
	function getBackUrl()
	{
		return $this->getUrl('customer/account/');
	}

	/**
	 * Sets all options render configurations
	 *
	 * @deprecated after 1.6.2.0
	 * @param null|array $optionCfg
	 * @return $this
	 */
	function setOptionsRenderCfgs($optionCfg)
	{
		$this->_optionsCfg = $optionCfg;
		return $this;
	}

	/**
	 * Returns all options render configurations
	 *
	 * @deprecated after 1.6.2.0
	 * @return array
	 */
	function getOptionsRenderCfgs()
	{
		return $this->_optionsCfg;
	}

	/**
	 * Adds config for rendering product type options
	 *
	 * @deprecated after 1.6.2.0
	 * @param string $productType
	 * @param string $helperName
	 * @param null|string $template
	 * @return $this
	 */
	function addOptionsRenderCfg($productType, $helperName, $template = null)
	{
		$this->_optionsCfg[$productType] = ['helper' => $helperName, 'template' => $template];
		return $this;
	}

	/**
	 * Returns html for showing item options
	 *
	 * @deprecated after 1.6.2.0
	 * @param string $productType
	 * @return array|null
	 */
	function getOptionsRenderCfg($productType)
	{
		if (isset($this->_optionsCfg[$productType])) {
			return $this->_optionsCfg[$productType];
		} elseif (isset($this->_optionsCfg['default'])) {
			return $this->_optionsCfg['default'];
		} else {
			return null;
		}
	}

	/**
	 * Returns html for showing item options
	 *
	 * @deprecated after 1.6.2.0
	 * @param Mage_Wishlist_Model_Item $item
	 * @return string
	 */
	function getDetailsHtml(Mage_Wishlist_Model_Item $item)
	{
		$cfg = $this->getOptionsRenderCfg($item->getProduct()->getTypeId());
		if (!$cfg) {
			return '';
		}

		$helper = Mage::helper($cfg['helper']);
		if (!($helper instanceof Mage_Catalog_Helper_Product_Configuration_Interface)) {
			Mage::throwException($this->__("Helper for wishlist options rendering doesn't implement required interface."));
		}

		$block = $this->getChild('item_options');
		if (!$block) {
			return '';
		}

		if ($cfg['template']) {
			$template = $cfg['template'];
		} else {
			$cfgDefault = $this->getOptionsRenderCfg('default');
			if (!$cfgDefault) {
				return '';
			}
			$template = $cfgDefault['template'];
		}

		return $block->setTemplate($template)
			->setOptionList($helper->getOptions($item))
			->toHtml();
	}

	/**
	 * Returns qty to show visually to user
	 *
	 * @deprecated after 1.6.2.0
	 * @param Mage_Wishlist_Model_Item $item
	 * @return float
	 */
	function getAddToCartQty(Mage_Wishlist_Model_Item $item)
	{
		$qty = $this->getQty($item);
		return $qty ? $qty : 1;
	}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getTitle():?string {return $this[self::$TITLE];}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--4/app/design/frontend/default/mobileshoppe/layout/wishlist.xml#L41
	 */
	final function setTitle(string $v):void {$this[self::$TITLE] = $v;}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getTitle()
	 * @used-by self::setTitle()
	 * @const string
	 */
	private static $TITLE = 'title';
}