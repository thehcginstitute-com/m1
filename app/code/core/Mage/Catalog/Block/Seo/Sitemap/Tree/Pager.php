<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 * @todo       Separate order, mode and pager
 */
class Mage_Catalog_Block_Seo_Sitemap_Tree_Pager extends Mage_Page_Block_Html_Pager
{
	protected $_showPerPage     = false;
	protected $_lastPageNumber  = 1;
	protected $_totalNum        = 0;
	protected $_firstNum        = 0;
	protected $_lastNum         = 1;

	/**
	 * @param int $displacement
	 * @return int
	 * @throws Exception
	 */
	function getCurrentPage($displacement = 0)
	{
		if ($page = (int) $this->getRequest()->getParam($this->getPageVarName()) + $displacement) {
			if ($page > $this->getLastPageNum()) {
				return $this->getLastPageNum();
			}
			return $page;
		}
		return 1;
	}

	/**
	 * @return int
	 */
	function getLimit()
	{
		$limits = $this->getAvailableLimit();
		$limits = array_keys($limits);
		return $limits[0];
	}

	/**
	 * @param Varien_Data_Collection $collection
	 * @return $this|Mage_Page_Block_Html_Pager
	 */
	function setCollection($collection)
	{
		$this->_collection = $collection;
		return $this;
	}

	/**
	 * @return Mage_Core_Model_Resource_Db_Collection_Abstract
	 */
	function getCollection()
	{
		return $this->_collection;
	}

	/**
	 * @return int
	 */
	function getFirstNum()
	{
		return $this->_firstNum + 1;
	}

	/**
	 * @param int $firstNum
	 * @return $this
	 */
	function setFirstNum($firstNum)
	{
		$this->_firstNum = $firstNum;
		return $this;
	}

	/**
	 * @return int
	 */
	function getLastNum()
	{
		return $this->_lastNum;
	}

	/**
	 * @param int $lastNum
	 * @return $this
	 */
	function setLastNum($lastNum)
	{
		$this->_lastNum = $lastNum;
		return $this;
	}

	/**
	 * @return int
	 */
	function getTotalNum()
	{
		return $this->_totalNum;
	}

	/**
	 * @param int $totalNum
	 * @return $this
	 */
	function setTotalNum($totalNum)
	{
		$this->_totalNum = $totalNum;
		return $this;
	}

	/**
	 * @return bool
	 */
	function isFirstPage()
	{
		return $this->getCurrentPage() == 1;
	}

	/**
	 * @return int
	 */
	function getLastPageNum()
	{
		return $this->_lastPageNumber;
	}

	/**
	 * @param int $lastPageNum
	 * @return $this
	 */
	function setLastPageNum($lastPageNum)
	{
		$this->_lastPageNumber = $lastPageNum;
		return $this;
	}

	/**
	 * @return bool
	 */
	function isLastPage()
	{
		return $this->getCurrentPage() >= $this->getLastPageNum();
	}

	/**
	 * @return array
	 */
	function getPages()
	{
		$pages = [];
		if ($this->getLastPageNum() <= $this->_displayPages) {
			$pages = range(1, $this->getLastPageNum());
		} else {
			$half = ceil($this->_displayPages / 2);
			if ($this->getCurrentPage() >= $half && $this->getCurrentPage() <= $this->getLastPageNum() - $half) {
				$start  = ($this->getCurrentPage() - $half) + 1;
				$finish = ($start + $this->_displayPages) - 1;
			} elseif ($this->getCurrentPage() < $half) {
				$start  = 1;
				$finish = $this->_displayPages;
			} elseif ($this->getCurrentPage() > ($this->getLastPageNum() - $half)) {
				$finish = $this->getLastPageNum();
				$start  = $finish - $this->_displayPages + 1;
			}
			$pages = range($start, $finish);
		}

		return $pages;
	}

	/**
	 * @return string
	 */
	function getPreviousPageUrl()
	{
		return $this->getPageUrl($this->getCurrentPage(-1));
	}

	/**
	 * @return string
	 */
	function getNextPageUrl()
	{
		return $this->getPageUrl($this->getCurrentPage(+1));
	}

	/**
	 * @return string
	 */
	function getLastPageUrl()
	{
		return $this->getPageUrl($this->getLastPageNum());
	}
}
