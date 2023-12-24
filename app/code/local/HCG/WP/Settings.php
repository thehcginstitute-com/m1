<?php
# 2023-12-24
final class HCG_WP_Settings {
	/**
	 * 2023-12-24
	 * "The main logo link address should not be hardcoded": https://github.com/thehcginstitute-com/m1/issues/72
	 * @used-by app/design/frontend/default/mobileshoppe/template/page/html/header.phtml
	 */
	static function url():string {return
		(trim(Mage::getStoreConfig('hcg_wp/primary/url'), '/ ') ?: 'https://www.thehcginstitute.com') . '/'
	;}
}