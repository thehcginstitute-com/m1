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
	 * @param string $text
	 * @return $this
	 */
	function setText(string $v):self {
		$this['text'] = $v;
		return $this;
	}

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
