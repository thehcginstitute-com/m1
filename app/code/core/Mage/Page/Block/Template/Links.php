<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Page
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Simple links list block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Template_Links extends Mage_Core_Block_Template
{
	/**
	 * All links
	 *
	 * @var array
	 */
	protected $_links = [];

	/**
	 * Cache key info
	 *
	 * @var null|array
	 */
	protected $_cacheKeyInfo = null;

	/**
	 * Set default template
	 *
	 */
	protected function _construct()
	{
		$this->setTemplate('page/template/links.phtml');
	}

	/**
	 * Get all links
	 *
	 * @return array
	 */
	function getLinks()
	{
		return $this->_links;
	}

	/**
	 * Add link to the list
	 * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @used-by Mage_Checkout_Block_Links::addCartLink()
	 * @used-by Mage_Checkout_Block_Links::addCheckoutLink()
	 * @used-by Mage_Persistent_Model_Observer::emulateAccountLinks()
	 * @param string|array $liParams
	 * @param string|array $aParams
	 */
	final function addLink(
		string $label,
		string $url = '',
		string $title = '',
		bool $prepare = false,
		array $urlParams = [],
		int $position = 0,
		$liParams = null,
		$aParams = null,
		string $beforeText = '',
		string $afterText = ''
	):self {
		$link = new Varien_Object([
			'label'         => $label,
			'url'           => ($prepare ? $this->getUrl($url, (is_array($urlParams) ? $urlParams : [])) : $url),
			'title'         => $title,
			'li_params'     => $this->_prepareParams($liParams),
			'a_params'      => $this->_prepareParams($aParams),
			'before_text'   => $beforeText,
			'after_text'    => $afterText,
		]);
		$this->_addIntoPosition($link, $position);
		return $this;
	}

	/**
	 * Add link into collection
	 *
	 * @param Varien_Object $link
	 * @param int $position
	 * @return $this
	 */
	protected function _addIntoPosition($link, $position)
	{
		$this->_links[$this->_getNewPosition($position)] = $link;

		if ((int) $position > 0) {
			ksort($this->_links);
		}

		return $this;
	}

	/**
	 * Add block to link list
	 * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	function addLinkBlock(string $name):self {
		$block = $this->getLayout()->getBlock($name);
		if ($block) {
			$position = (int)$block->getPosition();
			$this->_addIntoPosition($block, $position);
		}
		return $this;
	}

	/**
	 * Remove Link block by blockName
	 *
	 * @param string $blockName
	 * @return $this
	 */
	function removeLinkBlock($blockName)
	{
		foreach ($this->_links as $key => $link) {
			if ($link instanceof Mage_Core_Block_Abstract && $link->getNameInLayout() == $blockName) {
				unset($this->_links[$key]);
			}
		}
		return $this;
	}

	/**
	 * Removes link by url
	 *
	 * @param string $url
	 * @return $this
	 */
	function removeLinkByUrl($url)
	{
		foreach ($this->_links as $k => $v) {
			if ($v->getUrl() == $url) {
				unset($this->_links[$k]);
			}
		}

		return $this;
	}

	/**
	 * Get cache key informative items
	 * Provide string array key to share specific info item with FPC placeholder
	 *
	 * @return array
	 */
	function getCacheKeyInfo()
	{
		if (is_null($this->_cacheKeyInfo)) {
			$links = [];
			if (!empty($this->_links)) {
				foreach ($this->_links as $position => $link) {
					if ($link instanceof Varien_Object) {
						$links[$position] = $link->getData();
					}
				}
			}
			$this->_cacheKeyInfo = parent::getCacheKeyInfo() + [
				'links' => base64_encode(serialize($links)),
				'name' => $this->getNameInLayout()
				];
		}

		return $this->_cacheKeyInfo;
	}

	/**
	 * Prepare tag attributes
	 *
	 * @param string|array $params
	 * @return string
	 */
	protected function _prepareParams($params)
	{
		if (is_string($params)) {
			return $params;
		} elseif (is_array($params)) {
			$result = '';
			foreach ($params as $key => $value) {
				$result .= ' ' . $key . '="' . addslashes($value) . '"';
			}
			return $result;
		}
		return '';
	}

	/**
	 * Set first/last
	 *
	 * @inheritDoc
	 */
	protected function _beforeToHtml()
	{
		if (!empty($this->_links)) {
			reset($this->_links);
			$this->_links[key($this->_links)]->setIsFirst(true);
			end($this->_links);
			$this->_links[key($this->_links)]->setIsLast(true);
		}
		return parent::_beforeToHtml();
	}

	/**
	 * Return new link position in list
	 *
	 * @param int $position
	 * @return int
	 */
	protected function _getNewPosition($position = 0)
	{
		if ((int) $position > 0) {
			while (isset($this->_links[$position])) {
				$position++;
			}
		} else {
			$position = 0;
			foreach ($this->_links as $k => $v) {
				$position = $k;
			}
			$position += 10;
		}
		return $position;
	}

	/**
	 * Get tags array for saving cache
	 *
	 * @return array
	 */
	function getCacheTags()
	{
		if (Mage::getSingleton('customer/session')->isLoggedIn()) {
			$this->addModelTags(Mage::getSingleton('customer/session')->getCustomer());
		}

		return parent::getCacheTags();
	}
}
