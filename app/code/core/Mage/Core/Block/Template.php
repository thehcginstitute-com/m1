<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2015-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base html block
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setContentHeading(string $value)
 * @method $this setDestElementId(string $value)
 * @method $this setFormAction(string $value)
 * @method $this setIdSuffix(string $value)
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 * @method $this setDisplayMinimalPrice(bool $value)
 */
class Mage_Core_Block_Template extends Mage_Core_Block_Abstract
{
	public const XML_PATH_DEBUG_TEMPLATE_HINTS_ADMIN        = 'dev/debug/template_hints_admin';
	public const XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS_ADMIN = 'dev/debug/template_hints_blocks_admin';
	public const XML_PATH_DEBUG_TEMPLATE_HINTS              = 'dev/debug/template_hints';
	public const XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS       = 'dev/debug/template_hints_blocks';
	public const XML_PATH_TEMPLATE_ALLOW_SYMLINK            = 'dev/template/allow_symlink';

	/**
	 * View scripts directory
	 *
	 * @var string
	 */
	protected $_viewDir = '';

	/**
	 * Assigned variables for view
	 *
	 * @var array
	 */
	protected $_viewVars = [];

	protected $_baseUrl;

	protected $_jsUrl;

	protected static $_showTemplateHintsAdmin;
	protected static $_showTemplateHintsBlocksAdmin;
	protected static $_showTemplateHints;
	protected static $_showTemplateHintsBlocks;

	/**
	 * Path to template file in theme.
	 *
	 * @var string|null
	 */
	protected $_template;

	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct()
	{
		parent::_construct();

		/*
		 * In case template was passed through constructor
		 * we assign it to block's property _template
		 * Mainly for those cases when block created
		 * not via Mage_Core_Model_Layout::addBlock()
		 */
		if ($this->hasData('template')) {
			$this->setTemplate($this->getData('template'));
		}
	}

	/**
	 * Get relevant path to template
	 *
	 * @return string
	 */
	function getTemplate()
	{
		return $this->_template;
	}

	/**
	 * Set path to template used for generating block's output.
	 *
	 * @param string $template
	 * @return $this
	 */
	function setTemplate($template)
	{
		$this->_template = $template;
		return $this;
	}

	/**
	 * Get absolute path to template
	 *
	 * @return string
	 */
	function getTemplateFile()
	{
		$params = ['_relative' => true];
		$area = $this->getArea();
		if ($area) {
			$params['_area'] = $area;
		}
		return Mage::getDesign()->getTemplateFilename($this->getTemplate(), $params);
	}

	/**
	 * Get design area
	 * @return string
	 */
	function getArea()
	{
		return $this->_getData('area');
	}

	/**
	 * Assign variable
	 *
	 * @param   string|array $key
	 * @param   mixed $value
	 * @return  $this
	 */
	function assign($key, $value = null)
	{
		if (is_array($key)) {
			foreach ($key as $k => $v) {
				$this->assign($k, $v);
			}
		} else {
			$this->_viewVars[$key] = $value;
		}
		return $this;
	}

	/**
	 * Set template location directory
	 *
	 * @param string $dir
	 * @return $this
	 */
	function setScriptPath($dir)
	{
		if (strpos($dir, '..') === false && ($dir === Mage::getBaseDir('design') || strpos(realpath($dir), realpath(Mage::getBaseDir('design'))) === 0)) {
			$this->_viewDir = $dir;
		} else {
			Mage::log('Not valid script path:' . $dir, Zend_Log::CRIT, null, true);
		}
		return $this;
	}

	/**
	 * Check if direct output is allowed for block
	 *
	 * @return bool
	 */
	function getDirectOutput()
	{
		if ($this->getLayout()) {
			return $this->getLayout()->getDirectOutput();
		}
		return false;
	}

	/**
	 * @return bool
	 */
	function getShowTemplateHintsAdmin()
	{
		if (is_null(self::$_showTemplateHintsAdmin)) {
			self::$_showTemplateHintsAdmin = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_ADMIN)
				&& Mage::helper('core')->isDevAllowed();
			self::$_showTemplateHintsBlocksAdmin = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS_ADMIN)
				&& Mage::helper('core')->isDevAllowed();
		}
		return self::$_showTemplateHintsAdmin;
	}

	/**
	 * @return bool
	 */
	function getShowTemplateHints()
	{
		if (is_null(self::$_showTemplateHints)) {
			self::$_showTemplateHints = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS)
				&& Mage::helper('core')->isDevAllowed();
			self::$_showTemplateHintsBlocks = Mage::getStoreConfig(self::XML_PATH_DEBUG_TEMPLATE_HINTS_BLOCKS)
				&& Mage::helper('core')->isDevAllowed();
		}
		return self::$_showTemplateHints;
	}

	/**
	 * Retrieve block view from file (template)
	 *
	 * @param   string $fileName
	 * @return  string
	 */
	function fetchView($fileName)
	{
		Varien_Profiler::start($fileName);

		// EXTR_SKIP protects from overriding
		// already defined variables
		extract($this->_viewVars, EXTR_SKIP);
		$do = $this->getDirectOutput();

		$hints = Mage::app()->getStore()->isAdmin() ? $this->getShowTemplateHintsAdmin() : $this->getShowTemplateHints();

		if (!$do) {
			ob_start();
		}
		if ($hints) {
			echo <<<HTML
<div style="position:relative; border:1px dotted red; margin:6px 2px; padding:18px 2px 2px 2px; zoom:1;">
<div style="position:absolute; left:0; top:0; padding:2px 5px; background:red; color:white; font:normal 11px Arial;
text-align:left !important; z-index:998;" onmouseover="this.style.zIndex='999'"
onmouseout="this.style.zIndex='998'" title="{$fileName}">{$fileName}</div>
HTML;
			if (Mage::app()->getStore()->isAdmin() ? self::$_showTemplateHintsBlocksAdmin : self::$_showTemplateHintsBlocks) {
				$thisClass = get_class($this);
				echo <<<HTML
<div style="position:absolute; right:0; top:0; padding:2px 5px; background:red; color:blue; font:normal 11px Arial;
text-align:left !important; z-index:998;" onmouseover="this.style.zIndex='999'" onmouseout="this.style.zIndex='998'"
title="{$thisClass}">{$thisClass}</div>
HTML;
			}
		}

		try {
			if (strpos($this->_viewDir . DS . $fileName, '..') === false
				&&
				($this->_viewDir == Mage::getBaseDir('design') || strpos(realpath($this->_viewDir), realpath(Mage::getBaseDir('design'))) === 0)
			) {
				# 2024-09-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# "Magento should log the error instead of failing with the `net::ERR_CONNECTION_RESET` code
				# («This site can’t be reached» / «The connection was reset») on an invalid path of a block's template":
				# https://github.com/cabinetsbay/site/issues/670
				# 2024-09-02
				# "How did I force Magento 1 to log the problem instead of failing with the `net::ERR_CONNECTION_RESET` code
				# («This site can’t be reached» / «The connection was reset») on an invalid path of a block's template?":
				# https://mage2.pro/t/6483
				/** @var string $f */
				df_assert(file_exists($f = df_cc_path($this->_viewDir, $fileName)), "The template is absent: `{$f}`.");
				include $f;
			} else {
				$thisClass = get_class($this);
				Mage::log('Not valid template file:' . $fileName . ' class: ' . $thisClass, Zend_Log::CRIT, null, true);
			}
		} catch (Throwable $e) {
			if (!$do) {
				ob_get_clean();
				$do = true;
			}
			# 2024-09-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Magento should log the error instead of failing with the `net::ERR_CONNECTION_RESET` code
			# («This site can’t be reached» / «The connection was reset») on an invalid path of a block's template":
			# https://github.com/cabinetsbay/site/issues/670
			# 2024-09-02
			# "How did I force Magento 1 to log the problem instead of failing with the `net::ERR_CONNECTION_RESET` code
			# («This site can’t be reached» / «The connection was reset») on an invalid path of a block's template?":
			# https://mage2.pro/t/6483
			df_log($e);
			if (Mage::getIsDeveloperMode()) {
				throw $e;
			}
		}

		if ($hints) {
			echo '</div>';
		}

		if (!$do) {
			$html = ob_get_clean();
		} else {
			$html = '';
		}
		Varien_Profiler::stop($fileName);
		return $html;
	}

	/**
	 * Render block
	 *
	 * @return string
	 */
	function renderView()
	{
		$this->setScriptPath(Mage::getBaseDir('design'));
		return $this->fetchView($this->getTemplateFile());
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		if (!$this->getTemplate()) {
			return '';
		}
		return $this->renderView();
	}

	/**
	 * Get base url of the application
	 *
	 * @return string
	 */
	function getBaseUrl()
	{
		if (!$this->_baseUrl) {
			$this->_baseUrl = Mage::getBaseUrl();
		}
		return $this->_baseUrl;
	}

	/**
	 * Get url of base javascript file
	 *
	 * To get url of skin javascript file use getSkinUrl()
	 *
	 * @param string $fileName
	 * @return string
	 */
	function getJsUrl($fileName = '')
	{
		if (!$this->_jsUrl) {
			$this->_jsUrl = Mage::getBaseUrl('js');
		}
		return $this->_jsUrl . $fileName;
	}

	/**
	 * Get data from specified object
	 *
	 * @param Varien_Object $object
	 * @param string $key
	 * @return mixed
	 */
	function getObjectData(Varien_Object $object, $key)
	{
		return $object->getDataUsingMethod((string)$key);
	}

	/**
	 * @inheritDoc
	 */
	function getCacheKeyInfo()
	{
		return [
			'BLOCK_TPL',
			Mage::app()->getStore()->getCode(),
			$this->getTemplateFile(),
			'template' => $this->getTemplate()
		];
	}

	/**
	 * Get is allowed symlinks flag
	 *
	 * @deprecated
	 * @return bool
	 */
	protected function _getAllowSymlinks()
	{
		return false;
	}
}
