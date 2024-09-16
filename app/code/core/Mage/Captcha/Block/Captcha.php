<?php
class Mage_Captcha_Block_Captcha extends Mage_Core_Block_Abstract {
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
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/adminhtml/default/default/layout/captcha.xml#L12
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/adminhtml/default/default/layout/captcha.xml#L21
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L16
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L30
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L44
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L58
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L70
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L78
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L92
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L106
	 */
	final function setImgHeight(int $v):void {$this[self::IMG_HEIGHT] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/adminhtml/default/default/layout/captcha.xml#L12
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/adminhtml/default/default/layout/captcha.xml#L21
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L16
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L30
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L44
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L58
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L70
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L78
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L92
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--8/app/design/frontend/base/default/layout/captcha.xml#L106
	 */
	final function setImgWidth(int $v):void {$this[self::IMG_WIDTH] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::setImgHeight()
	 * @used-by Mage_Captcha_Block_Captcha_Zend::getImgHeight()
	 */
	const IMG_HEIGHT = 'img_height';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::setImgWidth()
	 * @used-by Mage_Captcha_Block_Captcha_Zend::getImgWidth()
	 */
	const IMG_WIDTH = 'img_width';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getFormId()
	 * @used-by self::setFormId()
	 * @const string
	 */
	private static $FORM_ID = 'form_id';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @override
	 * @see Mage_Core_Block_Abstract::_toHtml()
	 * @used-by Mage_Core_Block_Abstract::toHtml()
	 */
	protected function _toHtml():string {
		if (Mage::helper('captcha')->isEnabled()) {
			$blockPath = Mage::helper('captcha')->getCaptcha($this->getFormId())->getBlockName();
			$block = $this->getLayout()->createBlock($blockPath);
			$block->setData($this->getData());
			return $block->toHtml();
		}
		return '';
	}
}
