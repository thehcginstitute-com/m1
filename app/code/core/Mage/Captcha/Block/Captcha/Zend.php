<?php
/**
 * @method string getFormId()
 * @method bool getIsAjax()
 */
class Mage_Captcha_Block_Captcha_Zend extends Mage_Core_Block_Template
{
	protected $_template = 'captcha/zend.phtml';

	/**
	 * @var string
	 */
	protected $_captcha;

	/**
	 * Returns template path
	 *
	 * @return string
	 */
	function getTemplate()
	{
		return $this->getIsAjax() ? '' : $this->_template;
	}

	/**
	 * Returns URL to controller action which returns new captcha image
	 *
	 * @return string
	 */
	function getRefreshUrl()
	{
		return Mage::getUrl(
			Mage::app()->getStore()->isAdmin() ? 'adminhtml/refresh/refresh' : 'captcha/refresh',
			['_secure' => Mage::app()->getStore()->isCurrentlySecure()]
		);
	}

	/**
	 * Renders captcha HTML (if required)
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (Mage::helper('captcha')->isEnabled() && $this->getCaptchaModel()->isRequired()) {
			$this->getCaptchaModel()->generate();
			return parent::_toHtml();
		}
		return '';
	}

	/**
	 * Returns captcha model
	 *
	 * @return Mage_Captcha_Model_Interface
	 */
	function getCaptchaModel()
	{
		return Mage::helper('captcha')->getCaptcha($this->getFormId());
	}
}
