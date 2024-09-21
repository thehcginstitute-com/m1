<?php
# 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Remove extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column {
	/**
	 * Retrieve block javascript
	 *
	 * @return string
	 */
	function getJs()
	{
		return parent::getJs() . "
		function confirmRemoveWishlistItem() {
			return confirm('"
			. Mage::helper('core')->jsQuoteEscape(
				$this->__('Are you sure you want to remove this product from your wishlist?')
			)
			. "');
		}
		";
	}
}