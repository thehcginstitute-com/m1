<?php
# 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
use Mage_Page_Block_Html as Root;
use Varien_Object as _DO;
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
	 * Apply page layout template (for old design packages)
	 * @used-by Mage_Catalog_CategoryController::viewAction()
	 * @used-by Mage_Catalog_Helper_Product_View::initProductLayout()
	 * @used-by Mage_Cms_Helper_Page::_renderPage()
	 * @used-by Mage_Review_ProductController::_initProductLayout()
	 */
	function applyTemplate(?string $pageLayout = null):self {
		if ($pageLayout === null) {
			$pageLayout = $this->getCurrentPageLayout();
		}
		else {
			$pageLayout = $this->_getConfig()->getPageLayout($pageLayout);
		}
		if (!$pageLayout) {
			return $this;
		}
		$root = $this->getLayout()->getBlock('root'); /** @var Root $root */
		if ($root && $root->canUseACustomTemplate()) {
			$root->setTemplate($pageLayout->getTemplate()); // If not applied handle
		}
		return $this;
	}

	/**
	 * Retrieve current applied page layout
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * @used-by self::applyTemplate()
	 * @used-by Mage_Catalog_Block_Product_Abstract::getPageLayout()
	 */
	function getCurrentPageLayout():?_DO {
		$r = null; /** @var ?_DO $r */
		$root = $this->getLayout()->getBlock('root'); /** @var ?Root $root */
		if ($root && ($lc = $root->getLayoutCode())) { /** @var ?string $lc */
			$r = $this->_getConfig()->getPageLayout($lc);
		}
		else {
			$handles = $this->getLayout()->getUpdate()->getHandles(); // All loaded handles
			$pageLayoutHandles = $this->_getConfig()->getPageLayoutHandles(); // Handles used in page layouts
			$appliedHandles = array_intersect($handles, $pageLayoutHandles); // Applied page layout handles
			if (!empty($appliedHandles)) {
				$currentHandle = array_pop($appliedHandles);
				$layoutCode = array_search($currentHandle, $pageLayoutHandles, true);
				$r = df_ftn($this->_getConfig()->getPageLayout($layoutCode));
			}
		}
		return $r;
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
