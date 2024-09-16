<?php
/**
 * A generic wrapper block that renders its children and supports a few parameters of the wrapper HTML-element
 * @method bool hasMayBeInvisible()
 * @method bool hasOtherParams()
 * @method string getOtherParams()
 */
class Mage_Page_Block_Html_Wrapper extends Mage_Core_Block_Abstract {
	/**
	 * Whether block should render its content if there are no children (no)
	 * @var bool
	 */
	protected $_dependsOnChildren = true;

	/**
	 * Render the wrapper element html
	 * Supports different optional parameters, set in data by keys:
	 * - element_tag_name (div by default)
	 * - element_id
	 * - element_class
	 * - element_other_attributes
	 *
	 * Renders all children inside the element.
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$html = empty($this->_children) ? '' : trim($this->getChildHtml('', true, true));
		if ($this->_dependsOnChildren && empty($html)) {
			return '';
		}
		if ($this->_isInvisible()) {
			return $html;
		}
		$otherParams = $this->hasOtherParams() ? ' ' . $this->getOtherParams() : '';
		return sprintf('<%1$s%2$s%3$s%4$s>%5$s</%1$s>',
			$this->getElementTagName()
			,($id = $this[self::$ELEMENT_ID]) ? " id='{$id}'" : ''
			,($с = $this[self::$ELEMENT_CLASS]) ? " class='{$с}'" : ''
			,$otherParams
			,$html
		);
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--3/app/design/frontend/default/mobileshoppe/layout/cms.xml#L13
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--3/app/design/frontend/default/mobileshoppe/layout/customer.xml#L156
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--3/app/design/frontend/default/mobileshoppe/layout/page.xml#L59
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--3/app/design/frontend/default/mobileshoppe/layout/page.xml#L64
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--3/app/design/frontend/default/mobileshoppe/layout/page.xml#L81
	 */
	final function setElementClass(string $v):void {$this[self::$ELEMENT_CLASS] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by https://github.com/thehcginstitute-com/m1/blob/2024-09-16--4/app/design/frontend/default/mobileshoppe/layout/checkout.xml#L345
	 */
	final function setElementId(string $v):void {$this[self::$ELEMENT_ID] = $v;}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_toHtml()
	 * @used-by self::setElementClass()
	 * @const string
	 */
	private static $ELEMENT_CLASS = 'element_class';

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_toHtml()
	 * @used-by self::setElementId()
	 * @const string
	 */
	private static $ELEMENT_ID = 'element_id';

	/**
	 * Wrapper element tag name getter
	 * @return string
	 */
	function getElementTagName()
	{
		$tagName = $this->_getData('html_tag_name');
		return $tagName ? $tagName : 'div';
	}

	/**
	 * Setter whether this block depends on children
	 * @param string $depends
	 * @return $this
	 */
	function dependsOnChildren($depends = '0')
	{
		$this->_dependsOnChildren = (bool)(int)$depends;
		return $this;
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 */
	final function markAsPotentiallyInvisible():void {$this->_potentiallyInvisible = true;}

	/**
	 * Whether the wrapper element should be eventually rendered.
	 * If it becomes "invisible", the behaviour will be somewhat similar to `core/text_list`.
	 */
	protected function _isInvisible():bool {
		if (!$this->_potentiallyInvisible) {
			return false;
		}
		foreach ($this->_children as $child) {
			if ($child->hasWrapperMustBeVisible()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 2024-09-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::_isInvisible()
	 * @used-by self::markAsPotentiallyInvisible()
	 * @var bool
	 */
	private $_potentiallyInvisible = false;
}
