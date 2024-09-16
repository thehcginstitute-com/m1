<?php
/**
 * @method $this setSignupUrl(string $value)
 */
class Mage_ProductAlert_Block_Product_View extends Mage_Core_Block_Template {
	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--7/app/design/frontend/base/default/layout/productalert.xml#L18
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--7/app/design/frontend/base/default/layout/productalert.xml#L30
	 */
	function setHtmlClass(string $v):void {$this[self::$HTML_CLASS] = $v;}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--7/app/design/frontend/base/default/layout/productalert.xml#L19
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--7/app/design/frontend/base/default/layout/productalert.xml#L29
	 */
	function setSignupLabel(string $v):void {$this[self::$SIGNUP_LABEL] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by app/design/frontend/base/default/template/productalert/product/view.phtml
	 * @const string
	 */
	private static $HTML_CLASS = 'html_class';

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by app/design/frontend/base/default/template/productalert/product/view.phtml
	 * @const string
	 */
	private static $SIGNUP_LABEL = 'signup_label';

	/**
	 * Current product instance
	 *
	 * @var Mage_Catalog_Model_Product
	 */
	protected $_product = null;

	/**
	 * Helper instance
	 *
	 * @var Mage_ProductAlert_Helper_Data|null
	 */
	protected $_helper = null;

	/**
	 * Check whether the stock alert data can be shown and prepare related data
	 */
	function prepareStockAlertData()
	{
		if (!$this->_getHelper()->isStockAlertAllowed() || !$this->_product || $this->_product->isAvailable()) {
			$this->setTemplate('');
			return;
		}
		$this->setSignupUrl($this->_getHelper()->getSaveUrl('stock'));
	}

	/**
	 * Check whether the price alert data can be shown and prepare related data
	 */
	function preparePriceAlertData()
	{
		if (!$this->_getHelper()->isPriceAlertAllowed()
			|| !$this->_product || $this->_product->getCanShowPrice() === false
		) {
			$this->setTemplate('');
			return;
		}
		$this->setSignupUrl($this->_getHelper()->getSaveUrl('price'));
	}

	/**
	 * Get current product instance
	 *
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		$product = Mage::registry('current_product');
		if ($product && $product->getId()) {
			$this->_product = $product;
		}

		return parent::_prepareLayout();
	}

	/**
	 * Retrieve helper instance
	 *
	 * @return Mage_ProductAlert_Helper_Data
	 */
	protected function _getHelper()
	{
		if (is_null($this->_helper)) {
			$this->_helper = Mage::helper('productalert');
		}
		return $this->_helper;
	}
}
