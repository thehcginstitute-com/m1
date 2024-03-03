<?php
	class HetNieuweWeb_CustomerNavigation_Block_Customer_Account_Navigation extends Mage_Customer_Block_Account_Navigation
	{	
		function removeLinkByName() {
			$NavigationLinks = [
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
			
			foreach ($NavigationLinks as $link => $configName) {
				$display = Mage::getStoreConfig('customer_navigation/display/'.$configName);
				if (isset($this->_links[$link]) && !$display) {
					unset($this->_links[$link]);
				}
			}
		}
		
		function renameLinkByName() {
			$NavigationLinks = [
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
			
			foreach ($NavigationLinks as $link => $configName) {
				$name = Mage::getStoreConfig('customer_navigation/rename/'.$configName);
				if (isset($this->_links[$link]) && $name != '') {					
					$this->_links[$link]["label"] = $name;					
				}
			}
		}
	}
?>