<?php
class Mage_Captcha_Block_Captcha extends Mage_Core_Block_Template {
	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_toHtml()
	 */
	final function getFormId():?string {return $this[self::$FORM_ID];}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by Mage_Captcha_Adminhtml_RefreshController::refreshAction()
	 * @used-by Mage_Captcha_RefreshController::indexAction()
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/adminhtml/default/default/layout/captcha.xml#L10
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/adminhtml/default/default/layout/captcha.xml#L19
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L14
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L28
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L42
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L56
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L68
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L76
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L90
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--5/app/design/frontend/base/default/layout/captcha.xml#L104
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
	protected function _toHtml() {
		if (Mage::helper('captcha')->isEnabled()) {
			$blockPath = Mage::helper('captcha')->getCaptcha($this->getFormId())->getBlockName();
			$block = $this->getLayout()->createBlock($blockPath);
			$block->setData($this->getData());
			return $block->toHtml();
		}
		return '';
	}
}
