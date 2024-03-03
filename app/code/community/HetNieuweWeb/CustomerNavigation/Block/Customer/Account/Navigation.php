<?php
# 2024-03-03 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `HetNieuweWeb_CustomerNavigation` module": https://github.com/thehcginstitute-com/m1/issues/437
class HetNieuweWeb_CustomerNavigation_Block_Customer_Account_Navigation extends Mage_Customer_Block_Account_Navigation {
	/**
	 * 2024-03-03
	 * @used-by app/design/frontend/base/default/layout/hetnieuweweb_customernavigation.xml
	 */
	function removeLinkByName():void {
		$ll = [
			'account'=>'account'
			, 'account_edit'=>'account_edit'
			, 'address_book'=>'address_book'
			, 'orders'=>'orders'
			# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "Delete the unused «Billing Agreements» feature": https://github.com/thehcginstitute-com/m1/issues/400
			# 2) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
			, 'reviews'=>'reviews'
			, 'tags'=>'tags'
			, 'wishlist'=>'wishlist'
			, 'OAuth Customer Tokens'=>'oauth_customer_tokens'
			, 'newsletter'=>'newsletter'
			, 'downloadable_products'=>'downloadable_products'
		];
		foreach ($ll as $l => $c) {
			$display = Mage::getStoreConfig('customer_navigation/display/'.$c);
			if (isset($this->_links[$l]) && !$display) {
				unset($this->_links[$l]);
			}
		}
	}

	/**
	 * 2024-03-03
	 * @used-by app/design/frontend/base/default/layout/hetnieuweweb_customernavigation.xml
	 */
	function renameLinkByName():void {
		$ll = [
			'account'=>'account'
			, 'account_edit'=>'account_edit'
			, 'address_book'=>'address_book'
			, 'orders'=>'orders'
			# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "Delete the unused «Billing Agreements» feature": https://github.com/thehcginstitute-com/m1/issues/400
			# 2) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
			, 'reviews'=>'reviews'
			, 'tags'=>'tags'
			, 'wishlist'=>'wishlist'
			, 'OAuth Customer Tokens'=>'oauth_customer_tokens'
			, 'newsletter'=>'newsletter'
			, 'downloadable_products'=>'downloadable_products'
		];
		foreach ($ll as $l => $c) {
			$name = Mage::getStoreConfig('customer_navigation/rename/'.$c);
			if (isset($this->_links[$l]) && $name != '') {
				$this->_links[$l]["label"] = $name;
			}
		}
	}
}