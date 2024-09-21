<?php
# 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Comment extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column {
	/**
	 * Retrieve column javascript code
	 *
	 * @return string
	 */
	function getJs()
	{
		/** @var Mage_Wishlist_Helper_Data $helper */
		$helper = $this->helper('wishlist');

		return parent::getJs() . "
		function focusComment(obj) {
			if( obj.value == '" . $helper->defaultCommentString() . "' ) {
				obj.value = '';
			} else if( obj.value == '' ) {
				obj.value = '" . $helper->defaultCommentString() . "';
			}
		}
		";
	}
}