<?php
/**
 * @method $this unsWithoutPrice()
 * @method $this setWithoutPrice(bool $value)
 */
class Mage_Bundle_Block_Catalog_Product_Price extends Mage_Catalog_Block_Product_Price {
	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setMAPTemplate(string $v):void {$this->_mapTemplate = $v;}

	/**
	 * @return bool
	 */
	function isRatesGraterThenZero()
	{
		$_request = Mage::getSingleton('tax/calculation')->getDefaultRateRequest();
		$_request->setProductClassId($this->getProduct()->getTaxClassId());
		$defaultTax = Mage::getSingleton('tax/calculation')->getRate($_request);

		$_request = Mage::getSingleton('tax/calculation')->getRateRequest();
		$_request->setProductClassId($this->getProduct()->getTaxClassId());
		$currentTax = Mage::getSingleton('tax/calculation')->getRate($_request);

		return ((float) $defaultTax > 0 || (float) $currentTax > 0);
	}

	/**
	 * Check if we have display prices including and excluding tax
	 * With corrections for Dynamic prices
	 *
	 * @return bool
	 * @throws Mage_Core_Model_Store_Exception
	 */
	function displayBothPrices()
	{
		$product = $this->getProduct();
		if ($product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC &&
			$product->getPriceModel()->getIsPricesCalculatedByIndex() !== false
		) {
			return false;
		}

		/** @var Mage_Tax_Helper_Data $helper */
		$helper = $this->helper('tax');
		return $helper->displayBothPrices(Mage::app()->getStore()->getId());
	}

	/**
	 * Convert block to html string
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$product = $this->getProduct();
		if ($this->_mapTemplate && Mage::helper('catalog')->canApplyMsrp($product)
				&& $product->getPriceType() != Mage_Bundle_Model_Product_Price::PRICE_TYPE_DYNAMIC
		) {
			$hiddenPriceHtml = parent::_toHtml();
			if (Mage::helper('catalog')->isShowPriceOnGesture($product)) {
				$this->setWithoutPrice(true);
			}
			$realPriceHtml = parent::_toHtml();
			$this->unsWithoutPrice();
			$addToCartUrl  = $this->getLayout()->getBlock('product.info.bundle')->getAddToCartUrl($product);
			$product->setAddToCartUrl($addToCartUrl);
			$html = $this->getLayout()
				->createBlock('catalog/product_price')
				->setTemplate($this->_mapTemplate)
				->setRealPriceHtml($hiddenPriceHtml)
				->setPriceElementIdPrefix('bundle-price-')
				->setIdSuffix($this->getIdSuffix())
				->setProduct($product)
				->toHtml();

			return $realPriceHtml . $html;
		}

		return parent::_toHtml();
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_toHtml()
	 * @used-by self::setMAPTemplate()
	 * @var string
	 */
	private $_mapTemplate = '';
}