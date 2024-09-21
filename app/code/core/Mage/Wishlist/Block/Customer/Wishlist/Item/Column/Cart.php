<?php
# 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart extends Mage_Wishlist_Block_Customer_Wishlist_Item_Column {
	/**
	 * Returns qty to show visually to user
	 *
	 * @param Mage_Wishlist_Model_Item $item
	 * @return float
	 */
	function getAddToCartQty(Mage_Wishlist_Model_Item $item)
	{
		$qty = $item->getQty();
		return $qty ? $qty : 1;
	}

	/**
	 * Retrieve column related javascript code
	 *
	 * @return string
	 */
	function getJs()
	{
		$js = "
			function addWItemToCart(itemId) {
				addWItemToCartCustom(itemId, true)
			}

			function addWItemToCartCustom(itemId, sendGet) {
				var url = '';
				if (sendGet) {
					url = '" . $this->getItemAddToCartUrl('%item%') . "';
				} else {
					url = '" . $this->getItemAddToCartUrlCustom('%item%', false) . "';
				}
				url = url.replace('%item%', itemId);

				var form = document.getElementById('wishlist-view-form');
				if (form) {
					var input = form['qty[' + itemId + ']'];
					if (input) {
						var separator = (url.indexOf('?') >= 0) ? '&' : '?';
						url += separator + input.name + '=' + encodeURIComponent(input.value);
					}
				}
				if (sendGet) {
					window.location.href = encodeURI(url);
				} else {
					customFormSubmit(url, '" . json_encode(['form_key' => $this->getFormKey()]) . "', 'post');
				}
			}
		";

		$js .= parent::getJs();
		return $js;
	}
}
