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
 * Html page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 * @todo       Separate order, mode and pager
 */
class Mage_Page_Block_Html_Pager extends Mage_Core_Block_Template
{
    protected $_collection = null;
    protected $_pageVarName    = 'p';
    protected $_limitVarName   = 'limit';
    protected $_availableLimit = [10 => 10,20 => 20,50 => 50];
    protected $_dispersion     = 3;
    protected $_displayPages   = 5;
    protected $_showPerPage    = true;
    protected $_limit          = null;
    protected $_outputRequired = true;

    /**
     * Pages quantity per frame
     * @var int
     */
    protected $_frameLength = 5;

    /**
     * Next/previous page position relatively to the current frame
     * @var int
     */
    protected $_jump = 5;

    /**
     * Frame initialization flag
     * @var bool
     */
    protected $_frameInitialized = false;

    /**
     * Start page position in frame
     * @var int
     */
    protected $_frameStart;

    /**
     * Finish page position in frame
     * @var int
     */
    protected $_frameEnd;

    protected function _construct()
    {
        parent::_construct();
        $this->setData('show_amounts', true);
        $this->setData('use_container', true);
        $this->setTemplate('page/html/pager.phtml');
    }

    /**
     * Return current page
     *
     * @return int
     */
    function getCurrentPage()
    {
        if (is_object($this->_collection)) {
            return $this->_collection->getCurPage();
        }
        return (int) $this->getRequest()->getParam($this->getPageVarName(), 1);
    }

    /**
     * Return current page limit
     *
     * @return int
     */
    function getLimit()
    {
        if ($this->_limit !== null) {
            return $this->_limit;
        }
        $limits = $this->getAvailableLimit();
        if ($limit = $this->getRequest()->getParam($this->getLimitVarName())) {
            if (isset($limits[$limit])) {
                return $limit;
            }
        }
        $limits = array_keys($limits);
        return $limits[0];
    }

    /**
     * Setter for limit items per page
     *
     * @param int $limit
     * @return $this
     */
    function setLimit($limit)
    {
        $this->_limit = $limit;
        return $this;
    }

    /**
     * Set collection for pagination
     *
     * @param  Varien_Data_Collection $collection
     * @return $this
     */
    function setCollection($collection)
    {
        $this->_collection = $collection
            ->setCurPage($this->getCurrentPage());
        // If not int - then not limit
        if ((int) $this->getLimit()) {
            $this->_collection->setPageSize($this->getLimit());
        }

        $this->_setFrameInitialized(false);

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
     * @param string $varName
     * @return $this
     */
    function setPageVarName($varName)
    {
        $this->_pageVarName = $varName;
        return $this;
    }

    /**
     * @return string
     */
    function getPageVarName()
    {
        return $this->_pageVarName;
    }

    /**
     * @param string $varName
     * @return $this
     */
    function setShowPerPage($varName)
    {
        $this->_showPerPage = $varName;
        return $this;
    }

    /**
     * @return bool
     */
    function getShowPerPage()
    {
        if (count($this->getAvailableLimit()) <= 1) {
            return false;
        }
        return $this->_showPerPage;
    }

    /**
     * @param string $varName
     * @return $this
     */
    function setLimitVarName($varName)
    {
        $this->_limitVarName = $varName;
        return $this;
    }

    /**
     * @return string
     */
    function getLimitVarName()
    {
        return $this->_limitVarName;
    }

    /**
     * @param array $limits
     */
    function setAvailableLimit(array $limits)
    {
        $this->_availableLimit = $limits;
    }

    /**
     * @return array
     */
    function getAvailableLimit()
    {
        return $this->_availableLimit;
    }

    /**
     * @return int
     */
    function getFirstNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize() * ($collection->getCurPage() - 1) + 1;
    }

    /**
     * @return int
     */
    function getLastNum()
    {
        $collection = $this->getCollection();
        return $collection->getPageSize() * ($collection->getCurPage() - 1) + $collection->count();
    }

    /**
     * @return int
     */
    function getTotalNum()
    {
        return $this->getCollection()->getSize();
    }

    /**
     * @return bool
     */
    function isFirstPage()
    {
        return $this->getCollection()->getCurPage() == 1;
    }

    /**
     * @return int
     */
    function getLastPageNum()
    {
        return $this->getCollection()->getLastPageNumber();
    }

    /**
     * @return bool
     */
    function isLastPage()
    {
        return $this->getCollection()->getCurPage() >= $this->getLastPageNum();
    }

    /**
     * @param int $limit
     * @return bool
     */
    function isLimitCurrent($limit)
    {
        return $limit == $this->getLimit();
    }

    /**
     * @param int $page
     * @return bool
     */
    function isPageCurrent($page)
    {
        return $page == $this->getCurrentPage();
    }

    /**
     * @return array
     */
    function getPages()
    {
        $collection = $this->getCollection();

        $start = 1;
        $finish = 1;
        $pages = [];
        if ($collection->getLastPageNumber() <= $this->_displayPages) {
            $pages = range(1, $collection->getLastPageNumber());
        } else {
            $half = ceil($this->_displayPages / 2);
            if ($collection->getCurPage() >= $half
                && $collection->getCurPage() <= $collection->getLastPageNumber() - $half
            ) {
                $start  = ($collection->getCurPage() - $half) + 1;
                $finish = ($start + $this->_displayPages) - 1;
            } elseif ($collection->getCurPage() < $half) {
                $start  = 1;
                $finish = $this->_displayPages;
            } elseif ($collection->getCurPage() > ($collection->getLastPageNumber() - $half)) {
                $finish = $collection->getLastPageNumber();
                $start  = $finish - $this->_displayPages + 1;
            }

            $pages = range($start, $finish);
        }
        return $pages;
    }

    /**
     * @return string
     */
    function getFirstPageUrl()
    {
        return $this->getPageUrl(1);
    }

    /**
     * @return string
     */
    function getPreviousPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(-1));
    }

    /**
     * @return string
     */
    function getNextPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getCurPage(+1));
    }

    /**
     * @return string
     */
    function getLastPageUrl()
    {
        return $this->getPageUrl($this->getCollection()->getLastPageNumber());
    }

    /**
     * @param int $page
     * @return string
     */
    function getPageUrl($page)
    {
        return $this->getPagerUrl([$this->getPageVarName() => $page]);
    }

    /**
     * @param int $limit
     * @return string
     */
    function getLimitUrl($limit)
    {
        return $this->getPagerUrl([$this->getLimitVarName() => $limit]);
    }

    /**
     * @param array $params
     * @return string
     */
    function getPagerUrl($params = [])
    {
        $urlParams = [];
        $urlParams['_current']  = true;
        $urlParams['_escape']   = true;
        $urlParams['_use_rewrite']   = true;
        $urlParams['_query']    = $params;
        return $this->getUrl('*/*/*', $urlParams);
    }

    /**
     * Getter for $_frameStart
     *
     * @return int
     */
    function getFrameStart()
    {
        $this->_initFrame();
        return $this->_frameStart;
    }

    /**
     * Getter for $_frameEnd
     *
     * @return int
     */
    function getFrameEnd()
    {
        $this->_initFrame();
        return $this->_frameEnd;
    }

    /**
     * Return array of pages in frame
     *
     * @return array
     */
    function getFramePages()
    {
        $start = $this->getFrameStart();
        $end = $this->getFrameEnd();
        return range($start, $end);
    }

    /**
     * Return page number of Previous jump
     *
     * @return int|null
     */
    function getPreviousJumpPage()
    {
        if (!$this->getJump()) {
            return null;
        }
        $frameStart = $this->getFrameStart();
        if ($frameStart - 1 > 1) {
            return max(2, $frameStart - $this->getJump());
        }

        return null;
    }

    /**
     * Prepare URL for Previous Jump
     *
     * @return string
     */
    function getPreviousJumpUrl()
    {
        return $this->getPageUrl($this->getPreviousJumpPage());
    }

    /**
     * Return page number of Next jump
     *
     * @return int|null
     */
    function getNextJumpPage()
    {
        if (!$this->getJump()) {
            return null;
        }
        $frameEnd = $this->getFrameEnd();
        if ($this->getLastPageNum() - $frameEnd > 1) {
            return min($this->getLastPageNum() - 1, $frameEnd + $this->getJump());
        }

        return null;
    }

    /**
     * Prepare URL for Next Jump
     *
     * @return string
     */
    function getNextJumpUrl()
    {
        return $this->getPageUrl($this->getNextJumpPage());
    }

    /**
     * Getter for $_frameLength
     *
     * @return int
     */
    function getFrameLength()
    {
        return $this->_frameLength;
    }

    /**
     * Getter for $_jump
     *
     * @return int
     */
    function getJump()
    {
        return $this->_jump;
    }

    /**
     * Setter for $_frameLength
     *
     * @param int $frame
     * @return $this
     */
    function setFrameLength($frame)
    {
        $frame = abs((int) $frame);
        if ($frame == 0) {
            $frame = $this->_frameLength;
        }
        if ($this->getFrameLength() != $frame) {
            $this->_setFrameInitialized(false);
            $this->_frameLength = $frame;
        }

        return $this;
    }

    /**
     * Setter for $_jump
     *
     * @param int $jump
     * @return $this
     */
    function setJump($jump)
    {
        $jump = abs((int) $jump);
        if ($this->getJump() != $jump) {
            $this->_setFrameInitialized(false);
            $this->_jump = $jump;
        }

        return $this;
    }

    /**
     * Whether to show first page in pagination or not
     *
     * @return bool
     */
    function canShowFirst()
    {
        return $this->getJump() > 1 && $this->getFrameStart() > 1;
    }

    /**
     * Whether to show last page in pagination or not
     *
     * @return bool
     */
    function canShowLast()
    {
        return $this->getJump() > 1 && $this->getFrameEnd() < $this->getLastPageNum();
    }

    /**
     * Whether to show link to Previous Jump
     *
     * @return bool
     */
    function canShowPreviousJump()
    {
        return $this->getPreviousJumpPage() !== null;
    }

    /**
     * Whether to show link to Next Jump
     *
     * @return bool
     */
    function canShowNextJump()
    {
        return $this->getNextJumpPage() !== null;
    }

    /**
     * Initialize frame data, such as frame start, frame start etc.
     *
     * @return $this
     */
    protected function _initFrame()
    {
        if (!$this->isFrameInitialized()) {
            $start = 0;
            $end = 0;

            $collection = $this->getCollection();
            if ($collection->getLastPageNumber() <= $this->getFrameLength()) {
                $start = 1;
                $end = $collection->getLastPageNumber();
            } else {
                $half = ceil($this->getFrameLength() / 2);
                if ($collection->getCurPage() >= $half
                    && $collection->getCurPage() <= $collection->getLastPageNumber() - $half
                ) {
                    $start  = ($collection->getCurPage() - $half) + 1;
                    $end = ($start + $this->getFrameLength()) - 1;
                } elseif ($collection->getCurPage() < $half) {
                    $start  = 1;
                    $end = $this->getFrameLength();
                } elseif ($collection->getCurPage() > ($collection->getLastPageNumber() - $half)) {
                    $end = $collection->getLastPageNumber();
                    $start  = $end - $this->getFrameLength() + 1;
                }
            }
            $this->_frameStart = $start;
            $this->_frameEnd = $end;

            $this->_setFrameInitialized(true);
        }

        return $this;
    }

    /**
     * Setter for flag _frameInitialized
     *
     * @param bool $flag
     * @return $this
     */
    protected function _setFrameInitialized($flag)
    {
        $this->_frameInitialized = (bool)$flag;
        return $this;
    }

    /**
     * Check if frame data was initialized
     *
     * @return bool
     */
    function isFrameInitialized()
    {
        return $this->_frameInitialized;
    }

    /**
     * Getter for alternative text for Previous link in pagination frame
     *
     * @return string
     */
    function getAnchorTextForPrevious()
    {
        return Mage::getStoreConfig('design/pagination/anchor_text_for_previous');
    }

    /**
     * Getter for alternative text for Next link in pagination frame
     *
     * @return string
     */
    function getAnchorTextForNext()
    {
        return Mage::getStoreConfig('design/pagination/anchor_text_for_next');
    }

    /**
     * Set whether output of the pager is mandatory
     *
     * @param bool $isRequired
     * @return $this
     */
    function setIsOutputRequired($isRequired)
    {
        $this->_outputRequired = (bool)$isRequired;
        return $this;
    }

    /**
     * Determine whether the pagination should be eventually rendered
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_outputRequired || $this->getTotalNum() > $this->getLimit()) {
            return parent::_toHtml();
        }
        return '';
    }
}
