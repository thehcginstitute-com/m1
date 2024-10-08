<?php
/**
 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * @see Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Cart
 * @see Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Comment
 * @see Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Image
 * @see Mage_Wishlist_Block_Customer_Wishlist_Item_Column_Remove
 */
class Mage_Wishlist_Block_Customer_Wishlist_Item_Column extends Mage_Wishlist_Block_Abstract {
	/**
	 * Checks whether column should be shown in table
	 * @return true
	 */
	function isEnabled(){return true; }

	/**
	 * Retrieve block html
	 * @return string
	 */
	protected function _toHtml() {
		if ($this->isEnabled()) {
			return parent::_toHtml();
		}
		return '';
	}

	/**
	 * Retrieve child blocks html
	 * @param string $name
	 * @param Mage_Core_Block_Abstract $child
	 */
	protected function _beforeChildToHtml($name, $child) {$child->setItem($this->getItem());}

	/**
	 * Retrieve column related javascript code
	 * @return string
	 */
	function getJs() {
		$js = '';
		foreach ($this->getSortedChildBlocks() as $child) {
			$js .= $child->getJs();
		}
		return $js;
	}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function getTitle():?string {return $this[self::$TITLE];}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--5/app/design/frontend/default/mobileshoppe/layout/wishlist.xml#L54
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-21--5/app/design/frontend/default/mobileshoppe/layout/wishlist.xml#L61
	 */
	final function setTitle(string $v):void {$this[self::$TITLE] = $v;}

	/**
	 * 2024-09-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::getTitle()
	 * @used-by self::setTitle()
	 * @const string
	 */
	private static $TITLE = 'title';
}