<?php
/**
 * @method array getLiParams()
 * @method $this setLiParams(array $value)
 * @method array getAParams()
 * @method $this setAParams(array $value)
 * @method string getInnerText()
 * @method $this setInnerText(string $value)
 * @method string getAfterText()
 * @method $this setAfterText(string $value)
 */
class Mage_Core_Block_Text extends Mage_Core_Block_Abstract {
	/**
     * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by self::addText()
	 * @used-by Amasty_Notfound_Block_Adminhtml_Log_Edit_Form::_prepareForm()
	 * @used-by Mage_Adminhtml_Block_Catalog_Product_Composite_Fieldset::_toHtml()
	 * @used-by Mage_Core_Block_Text_List::_toHtml()
	 * @used-by Mage_Core_Block_Text_List_Item::_toHtml()
	 * @used-by Mage_Core_Block_Text_List_Link::_toHtml()
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--11/app/design/adminhtml/default/default/layout/main.xml#L195-L197
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-17--11/app/design/frontend/base/default/layout/ebizmarts/mailchimp.xml#L28-L30
	 */
	function setText(string $v):void {$this['text'] = $v;}

	/**
	 * @return string
	 */
	function getText()
	{
		return $this->getData('text');
	}

	/**
	 * @param string $text
	 * @param bool $before
	 */
	function addText($text, $before = false)
	{
		if ($before) {
			$this->setText($text . $this->getText());
		} else {
			$this->setText($this->getText() . $text);
		}
	}

	/**
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!$this->_beforeToHtml()) {
			return '';
		}

		return $this->getText();
	}
}
