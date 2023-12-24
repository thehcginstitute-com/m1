<?php
# 2023-12-24
final class HCG_Core_Settings {
	/**
	 * 2023-12-24
	 * "The main logo link address should not be hardcoded": https://github.com/thehcginstitute-com/m1/issues/72
	 * @used-by HCG_Core_Observer::controller_action_predispatch_cms_index_index()
	 * @used-by app/design/frontend/default/mobileshoppe/template/page/html/topmenu.phtml
	 */
	static function urlProducts():string {return Mage::getUrl('products');}
}