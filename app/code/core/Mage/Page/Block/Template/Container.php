<?php
# 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Page_Block_Template_Container extends Mage_Core_Block_Template {
	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by app/design/frontend/base/default/template/page/template/container.phtml
	 * @used-by app/design/frontend/base/default/template/catalog/seo/sitemap/container.phtml
	 */
	final function getTitle():?string {return $this[self::$TITLE];}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--3/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L393
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--4/app/design/frontend/default/mobileshoppe/layout/catalog.xml#L451
	 */
	final function setTitle(string $v):void {$this[self::$TITLE] = $v;}

	/**
	 * @override
	 * @see Mage_Core_Block_Template::_construct()
	 * @used-by Varien_Object::__construct()
	 */
	protected function _construct() {$this->setTemplate('page/template/container.phtml');}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getTitle()
	 * @used-by self::setTitle()
	 * @const string
	 */
	private static $TITLE = 'title';
}