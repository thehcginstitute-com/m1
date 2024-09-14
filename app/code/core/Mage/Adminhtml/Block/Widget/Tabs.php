<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Widget_Tabs extends Mage_Adminhtml_Block_Widget
{
	/**
	 * @var array
	 */
	protected $_tabs = [];

	/**
	 * For sorting tabs.
	 *
	 * @var array
	 */
	protected $_afterTabIds = [];

	/**
	 * For sorting tabs.
	 *
	 * @var array
	 */
	protected $_tabPositions = [];

	/**
	 * For sorting tabs.
	 *
	 * @var int
	 */
	protected $_tabPosition = 100;

	/**
	 * Active tab key
	 *
	 * @var string
	 */
	protected $_activeTab = null;

	/**
	 * Destination HTML element id
	 *
	 * @var string
	 */
	protected $_destElementId = 'content';

	protected function _construct()
	{
		$this->setTemplate('widget/tabs.phtml');
	}

	/**
	 * retrieve destination html element id
	 *
	 * @return string
	 */
	function getDestElementId()
	{
		return $this->_destElementId;
	}

	/**
	 * @param string $elementId
	 * @return $this
	 */
	function setDestElementId($elementId)
	{
		$this->_destElementId = $elementId;
		return $this;
	}

	/**
	 * Add new tab after another
	 * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @param   string|array|Varien_Object $block
	 */
	final function addTabAfter(string $id, $block, string $after):void {
		$this->addTab($id, $block);
		$this->_afterTabIds[$id] = $after;
	}

	/**
	 * Add new tab
	 * 2024-09-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 * @param string|array|Varien_Object $block
	 */
	function addTab(string $id, $block):self {
		if (is_array($block)) {
			$this->_tabs[$id] = new Varien_Object($block);
		} elseif ($block instanceof Varien_Object) {
			$this->_tabs[$id] = $block;
			if (!$this->_tabs[$id]->hasTabId()) {
				$this->_tabs[$id]->setTabId($id);
			}
		} elseif (is_string($block)) {
			if (strpos($block, '/')) {
				$this->_tabs[$id] = $this->getLayout()->createBlock($block);
			} elseif ($this->getChild($block)) {
				$this->_tabs[$id] = $this->getChild($block);
			} else {
				$this->_tabs[$id] = null;
			}

			if (!($this->_tabs[$id] instanceof Mage_Adminhtml_Block_Widget_Tab_Interface)) {
				throw new Exception(Mage::helper('adminhtml')->__('Wrong tab configuration.'));
			}
		} else {
			throw new Exception(Mage::helper('adminhtml')->__('Wrong tab configuration.'));
		}

		if (is_null($this->_tabs[$id]->getUrl())) {
			$this->_tabs[$id]->setUrl('#');
		}

		if (!$this->_tabs[$id]->getTitle()) {
			$this->_tabs[$id]->setTitle($this->_tabs[$id]->getLabel());
		}

		$this->_tabs[$id]->setId($id);
		$this->_tabs[$id]->setTabId($id);

		if ($this->_tabs[$id]->getActive() === true) {
			$this->setActiveTab($id);
		}

		// For sorting tabs.
		$this->_tabPositions[$id] = $this->_tabPosition;
		$this->_tabPosition += 100;
		if ($this->_tabs[$id]->getAfter()) {
			$this->_afterTabIds[$id] = $this->_tabs[$id]->getAfter();
		}

		return $this;
	}

	/**
	 * @return string
	 */
	function getActiveTabId()
	{
		return $this->getTabId($this->_tabs[$this->_activeTab]);
	}

	/**
	 * Set Active Tab
	 * Tab has to be not hidden and can show
	 *
	 * @param string $tabId
	 * @return $this
	 */
	function setActiveTab($tabId)
	{
		if (isset($this->_tabs[$tabId]) && $this->canShowTab($this->_tabs[$tabId])
			&& !$this->getTabIsHidden($this->_tabs[$tabId])
		) {
			$this->_activeTab = $tabId;
		}
		return $this;
	}

	/**
	 * Set Active Tab
	 *
	 * @param string $tabId
	 * @return $this
	 */
	protected function _setActiveTab($tabId)
	{
		foreach ($this->_tabs as $id => $tab) {
			if ($this->getTabId($tab) == $tabId) {
				$this->_activeTab = $id;
				$tab->setActive(true);
				return $this;
			}
		}
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function _beforeToHtml()
	{
		Mage::dispatchEvent('adminhtml_block_widget_tabs_html_before', ['block' => $this]);
		if ($activeTab = $this->getRequest()->getParam('active_tab')) {
			$this->setActiveTab($activeTab);
		} elseif ($activeTabId = Mage::getSingleton('admin/session')->getActiveTabId()) {
			$this->_setActiveTab($activeTabId);
		}

		if ($this->_activeTab === null && !empty($this->_tabs)) {
			$this->_activeTab = (reset($this->_tabs))->getId();
		}

		if (!empty($this->_afterTabIds)) {
			$this->_tabs = $this->_reorderTabs();
		}

		$this->assign('tabs', $this->_tabs);
		return parent::_beforeToHtml();
	}

	/**
	 * Find the root parent Tab ID recursively.
	 *
	 * @param string $currentAfterTabId
	 * @param int $degree Degrees of separation between child and root parent.
	 * @return string The parent tab ID.
	 */
	protected function _getRootParentTabId($currentAfterTabId, &$degree)
	{
		if (array_key_exists($currentAfterTabId, $this->_afterTabIds)) {
			$degree++;
			return $this->_getRootParentTabId($this->_afterTabIds[$currentAfterTabId], $degree);
		} else {
			return $currentAfterTabId;
		}
	}

	/**
	 * @return array
	 */
	protected function _reorderTabs()
	{
		// Set new position based on $afterTabId.
		foreach ($this->_afterTabIds as $tabId => $afterTabId) {
			if (array_key_exists($afterTabId, $this->_tabs)) {
				$degree = 1; // Initialize to 1 degree of separation.
				$parentAfterTabId = $this->_getRootParentTabId($afterTabId, $degree);
				$this->_tabPositions[$tabId] = $this->_tabPositions[$parentAfterTabId] + $degree;
				$degree++;
			}
		}

		asort($this->_tabPositions);

		$ordered = [];
		foreach ($this->_tabPositions as $tabId => $position) {
			if (isset($this->_tabs[$tabId])) {
				$tab = $this->_tabs[$tabId];
				$ordered[$tabId] = $tab;
			}
		}

		return $ordered;
	}

	/**
	 * @return string
	 */
	function getJsObjectName()
	{
		return $this->getId() . 'JsTabs';
	}

	/**
	 * @return array
	 */
	function getTabsIds()
	{
		if (empty($this->_tabs)) {
			return [];
		}
		return array_keys($this->_tabs);
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @param bool $withPrefix
	 * @return string
	 */
	function getTabId($tab, $withPrefix = true)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			return ($withPrefix ? $this->getId() . '_' : '') . $tab->getTabId();
		}
		return ($withPrefix ? $this->getId() . '_' : '') . $tab->getId();
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return bool
	 */
	function canShowTab($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			return $tab->canShowTab();
		}
		return true;
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return bool
	 */
	function getTabIsHidden($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			return $tab->isHidden();
		}
		return $tab->getIsHidden();
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return string
	 */
	function getTabUrl($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			if (method_exists($tab, 'getTabUrl')) {
				return $tab->getTabUrl();
			}
			return '#';
		}
		if (!is_null($tab->getUrl())) {
			return $tab->getUrl();
		}
		return '#';
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return string
	 */
	function getTabTitle($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			return $tab->getTabTitle();
		}
		return $tab->getTitle();
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return string
	 */
	function getTabClass($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			if (method_exists($tab, 'getTabClass')) {
				return $tab->getTabClass();
			}
			return '';
		}
		return (string) $tab->getClass();
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return string
	 */
	function getTabLabel($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			return $this->escapeHtml($tab->getTabLabel());
		}
		return $this->escapeHtml($tab->getLabel());
	}

	/**
	 * @param Varien_Object|Mage_Adminhtml_Block_Widget_Tab_Interface $tab
	 * @return string
	 */
	function getTabContent($tab)
	{
		if ($tab instanceof Mage_Adminhtml_Block_Widget_Tab_Interface) {
			if ($tab->getSkipGenerateContent()) {
				return '';
			}
			return $tab->toHtml();
		}
		return $tab->getContent();
	}

	/**
	 * Mark tabs as dependant of each other
	 * Arbitrary number of tabs can be specified, but at least two
     * 2024-09-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	function bindShadowTabs(string $first, string $second):void {
		$tabs = [];
		$args = func_get_args();
		if ((!empty($args)) && (count($args) > 1)) {
			foreach ($args as $tabId) {
				if (isset($this->_tabs[$tabId])) {
					$tabs[$tabId] = $tabId;
				}
			}
			$blockId = $this->getId();
			foreach ($tabs as $tabId) {
				foreach ($tabs as $tabToId) {
					if ($tabId !== $tabToId) {
						if (!$this->_tabs[$tabToId]->getData('shadow_tabs')) {
							$this->_tabs[$tabToId]->setData('shadow_tabs', []);
						}
						$this->_tabs[$tabToId]->setData('shadow_tabs', array_merge(
							$this->_tabs[$tabToId]->getData('shadow_tabs'),
							[$blockId . '_' . $tabId]
						));
					}
				}
			}
		}
	}

	/**
	 * Obtain shadow tabs information
	 *
	 * @param bool $asJson
	 * @return array|string
	 */
	function getAllShadowTabs($asJson = true)
	{
		$result = [];
		if (!empty($this->_tabs)) {
			$blockId = $this->getId();
			foreach (array_keys($this->_tabs) as $tabId) {
				if ($this->_tabs[$tabId]->getData('shadow_tabs')) {
					$result[$blockId . '_' . $tabId] = $this->_tabs[$tabId]->getData('shadow_tabs');
				}
			}
		}
		if ($asJson) {
			return Mage::helper('core')->jsonEncode($result);
		}
		return $result;
	}

	/**
	 * Set tab property by tab's identifier
	 *
	 * @param string $tab
	 * @param string $key
	 * @param mixed $value
	 * @return $this
	 */
	function setTabData($tab, $key, $value)
	{
		if (isset($this->_tabs[$tab]) && $this->_tabs[$tab] instanceof Varien_Object) {
			if ($key == 'url') {
				$value = $this->getUrl($value, ['_current' => true, '_use_rewrite' => true]);
			}
			$this->_tabs[$tab]->setData($key, $value);
		}

		return $this;
	}

	/**
	 * Removes tab with passed id from tabs block
	 *
	 * @param string $tabId
	 * @return $this
	 */
	function removeTab($tabId)
	{
		if (isset($this->_tabs[$tabId])) {
			unset($this->_tabs[$tabId]);
		}
		return $this;
	}
}
