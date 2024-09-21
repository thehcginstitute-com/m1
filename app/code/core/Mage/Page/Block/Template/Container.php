<?php
# 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Page_Block_Template_Container extends Mage_Core_Block_Template {
	/**
	 * @override
	 * @see Mage_Core_Block_Template::_construct()
	 * @used-by Varien_Object::__construct()
	 */
	protected function _construct() {$this->setTemplate('page/template/container.phtml');}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @const string
	 */
	private static $TITLE = 'title';
}