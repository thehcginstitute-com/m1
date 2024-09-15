<?php
/**
 * @method $this setIsWishlistActive(bool $value)
 * @method int getItemsCount()
 * @method Mage_Sales_Model_Quote_Item[] getCustomItems()
 */
class Mage_Checkout_Block_Cart extends Mage_Checkout_Block_Cart_Abstract {
	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16/app/design/frontend/default/mobileshoppe/layout/checkout.xml#L54
	 */
	function setCartTemplate(string $v):void {$this[self::$CART_TEMPLATE] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16/app/design/frontend/default/mobileshoppe/layout/checkout.xml#L55
	 */
	function setEmptyTemplate(string $v):void {$this[self::$EMPTY_TEMPLATE] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::chooseTemplate()
	 * @used-by self::setCartTemplate()
	 * @const string
	 */
	private static $CART_TEMPLATE = 'cart_template';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::chooseTemplate()
	 * @used-by self::setEmptyTemplate()
	 * @const string
	 */
	private static $EMPTY_TEMPLATE = 'empty_template';

	/**
	 * Prepare cart items URLs
	 *
	 * @deprecated after 1.7.0.2
	 */
	function prepareItemUrls()
	{
		$products = [];
		foreach ($this->getItems() as $item) {
			$product    = $item->getProduct();
			$option     = $item->getOptionByCode('product_type');
			if ($option) {
				$product = $option->getProduct();
			}

			if ($item->getStoreId() != Mage::app()->getStore()->getId()
				&& !$item->getRedirectUrl()
				&& !$product->isVisibleInSiteVisibility()
			) {
				$products[$product->getId()] = $item->getStoreId();
			}
		}

		if ($products) {
			$products = Mage::getResourceSingleton('catalog/url')
				->getRewriteByProductStore($products);
			foreach ($this->getItems() as $item) {
				$product    = $item->getProduct();
				$option     = $item->getOptionByCode('product_type');
				if ($option) {
					$product = $option->getProduct();
				}

				if (isset($products[$product->getId()])) {
					$object = new Varien_Object($products[$product->getId()]);
					$item->getProduct()->setUrlDataObject($object);
				}
			}
		}
	}

	/**
	 * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function chooseTemplate():void {$this->setTemplate($this[
		$this->getItemsCount() || $this->getQuote()->getItemsCount() ? self::$CART_TEMPLATE : self::$EMPTY_TEMPLATE
	]);}

	/**
	 * @return bool
	 */
	function hasError()
	{
		return $this->getQuote()->getHasError();
	}

	/**
	 * @return float|int|mixed
	 */
	function getItemsSummaryQty()
	{
		return $this->getQuote()->getItemsSummaryQty();
	}

	/**
	 * @return bool|mixed
	 */
	function isWishlistActive()
	{
		$isActive = $this->_getData('is_wishlist_active');
		if ($isActive === null) {
			$isActive = Mage::getStoreConfig('wishlist/general/active')
				&& Mage::getSingleton('customer/session')->isLoggedIn();
			$this->setIsWishlistActive($isActive);
		}
		return $isActive;
	}

	/**
	 * @return string
	 */
	function getCheckoutUrl()
	{
		return $this->getUrl('checkout/onepage', ['_secure' => true]);
	}

	/**
	 * Return "cart" form action url
	 *
	 * @return string
	 */
	function getFormActionUrl()
	{
		return $this->getUrl('checkout/cart/updatePost', ['_secure' => $this->_isSecure()]);
	}

	/**
	 * @return mixed|string
	 */
	function getContinueShoppingUrl()
	{
		$url = $this->getData('continue_shopping_url');
		if (is_null($url)) {
			$url = Mage::getSingleton('checkout/session')->getContinueShoppingUrl(true);
			if (!$url) {
				$url = Mage::getUrl();
			}
			$this->setData('continue_shopping_url', $url);
		}
		return $url;
	}

	/**
	 * @return bool
	 */
	function getIsVirtual()
	{
		/** @var Mage_Checkout_Helper_Cart $helper */
		$helper = $this->helper('checkout/cart');
		return $helper->getIsVirtualQuote();
	}

	/**
	 * Return list of available checkout methods
	 *
	 * @param string $nameInLayout Container block alias in layout
	 * @return array
	 */
	function getMethods($nameInLayout)
	{
		if ($this->getChild($nameInLayout) instanceof Mage_Core_Block_Abstract) {
			return $this->getChild($nameInLayout)->getSortedChildren();
		}
		return [];
	}

	/**
	 * Return HTML of checkout method (link, button etc.)
	 *
	 * @param string $name Block name in layout
	 * @return string
	 * @throws Mage_Core_Exception
	 */
	function getMethodHtml($name)
	{
		$block = $this->getLayout()->getBlock($name);
		if (!$block) {
			Mage::throwException(Mage::helper('checkout')->__('Invalid method: %s', $name));
		}
		return $block->toHtml();
	}

	/**
	 * Return customer quote items
	 *
	 * @return Mage_Sales_Model_Quote_Item[]
	 */
	function getItems()
	{
		if ($this->getCustomItems()) {
			return $this->getCustomItems();
		}

		return parent::getItems();
	}
}
