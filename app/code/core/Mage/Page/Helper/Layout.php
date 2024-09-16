<?php
class Mage_Page_Helper_Layout extends Mage_Core_Helper_Abstract {
	protected $_moduleName = 'Mage_Page';

	/**
	 * Apply page layout handle
	 *
	 * @param string $pageLayout
	 * @return $this
	 */
	function applyHandle($pageLayout)
	{
		$pageLayout = $this->_getConfig()->getPageLayout($pageLayout);

		if (!$pageLayout) {
			return $this;
		}

		$this->getLayout()
			->getUpdate()
			->addHandle($pageLayout->getLayoutHandle());

		return $this;
	}

	/**
	 * Apply page layout template
	 * (for old design packages)
	 *
	 * @param string $pageLayout
	 * @return $this
	 */
	function applyTemplate($pageLayout = null) {
		if ($pageLayout === null) {
			$pageLayout = $this->getCurrentPageLayout();
		}
		else {
			$pageLayout = $this->_getConfig()->getPageLayout($pageLayout);
		}
		if (!$pageLayout) {
			return $this;
		}
		if ($this->getLayout()->getBlock('root') && !$this->getLayout()->getBlock('root')->getIsHandle()) {
			// If not applied handle
			$this->getLayout()->getBlock('root')->setTemplate($pageLayout->getTemplate());
		}
		return $this;
	}

	/**
	 * Retrieve current applied page layout
	 *
	 * @return Varien_Object|false
	 */
	function getCurrentPageLayout()
	{
		if ($this->getLayout()->getBlock('root') &&
			$this->getLayout()->getBlock('root')->getLayoutCode()
		) {
			return $this->_getConfig()->getPageLayout($this->getLayout()->getBlock('root')->getLayoutCode());
		}

		// All loaded handles
		$handles = $this->getLayout()->getUpdate()->getHandles();
		// Handles used in page layouts
		$pageLayoutHandles = $this->_getConfig()->getPageLayoutHandles();
		// Applied page layout handles
		$appliedHandles = array_intersect($handles, $pageLayoutHandles);

		if (empty($appliedHandles)) {
			return false;
		}

		$currentHandle = array_pop($appliedHandles);

		$layoutCode = array_search($currentHandle, $pageLayoutHandles, true);

		return $this->_getConfig()->getPageLayout($layoutCode);
	}

	/**
	 * Retrieve page config
	 *
	 * @return Mage_Page_Model_Config
	 */
	protected function _getConfig()
	{
		return Mage::getSingleton('page/config');
	}
}
