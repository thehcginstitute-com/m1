<?php
class Mage_Captcha_Block_Captcha extends Mage_Core_Block_Template {
	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_toHtml()
	 */
	final function getFormId():?string {return $this[self::$FORM_ID];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function setFormId(string $v):void {$this[self::$FORM_ID] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getFormId()
	 * @used-by self::setFormId()
	 * @const string
	 */
	private static $FORM_ID = 'form_id';

	/**
	 * Renders captcha HTML (if required)
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (Mage::helper('captcha')->isEnabled()) {
			$blockPath = Mage::helper('captcha')->getCaptcha($this->getFormId())->getBlockName();
			$block = $this->getLayout()->createBlock($blockPath);
			$block->setData($this->getData());
			return $block->toHtml();
		}
		return '';
	}
}
