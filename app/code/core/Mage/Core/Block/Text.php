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
