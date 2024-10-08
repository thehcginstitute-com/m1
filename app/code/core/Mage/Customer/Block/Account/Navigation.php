<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account navigation sidebar
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Account_Navigation extends Mage_Core_Block_Template
{
	/**
	 * @var array
	 */
	protected $_links = [];

	/**
	 * @var bool
	 */
	protected $_activeLink = false;

	/**
	 * 2024-09-12 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function addLink(string $name, string $label, string $path = '', array $urlParams = []):self {
		$this->_links[$name] = new Varien_Object([
			'name' => $name,
			'path' => $path,
			'label' => $label,
			'url' => $this->getUrl($path, $urlParams),
		]);
		return $this;
	}

	/**
	 * Remove a link
	 *
	 * @param string $name Name of the link
	 * @return $this
	 */
	function removeLink($name)
	{
		if (isset($this->_links[$name])) {
			unset($this->_links[$name]);
		}
		return $this;
	}

	/**
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function setActive(string $path):self {
		$this->_activeLink = $this->_completePath($path);
		return $this;
	}

	/**
	 * @return array
	 */
	function getLinks()
	{
		return $this->_links;
	}

	/**
	 * @param Varien_Object $link
	 * @return bool
	 */
	function isActive($link)
	{
		if (empty($this->_activeLink)) {
			$this->_activeLink = $this->getAction()->getFullActionName('/');
		}
		if ($this->_completePath($link->getPath()) == $this->_activeLink) {
			return true;
		}
		return false;
	}

	/**
	 * @param string $path
	 * @return string
	 */
	protected function _completePath($path)
	{
		$path = rtrim($path, '/');
		switch (count(explode('/', $path))) {
			case 1:
				$path .= '/index';
				// no break

			case 2:
				$path .= '/index';
		}
		return $path;
	}
}
