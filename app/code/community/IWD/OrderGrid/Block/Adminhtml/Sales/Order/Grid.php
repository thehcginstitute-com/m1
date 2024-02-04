<?php
# 2023-01-16 Dmitrii Fediuk https://www.upwork.com/fl/mage2pro
# "Make the `IWD_OrderGrid` module compatible with the `Raveinfosys_Deleteorder` module":
# https://github.com/thehcginstitute-com/m1/issues/11
# 2023-12-01
# «Class 'Raveinfosys_Deleteorder_Block_Adminhtml_Sales_Order_Grid' not found
# in app/code/community/IWD/OrderGrid/Block/Adminhtml/Sales/Order/Grid.php on line 5»:
# https://github.com/thehcginstitute-com/m1/issues/30
class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{
    const XPATH_GRID_SAVED_LIMIT = 'iwd_ordermanager/grid_order/saved_params';
    const XPATH_IS_SAVE_GRID_PARAMS = 'iwd_ordermanager/grid_order/save_grid_params';

    protected $gridParams = null;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $this->setDefaultGridParams();
            $this->setColumnFilters(
                array('iwd_multiselect' => 'iwd_ordergrid/adminhtml_widget_grid_column_filter_multiselect')
            )->setColumnRenderers(
                array('iwd_multiselect' => 'adminhtml/widget_grid_column_renderer_options')
            );
        }
    }

    protected function isSaveGridParams()
    {
        return Mage::getStoreConfig(self::XPATH_IS_SAVE_GRID_PARAMS);
    }

    protected function setDefaultGridParams()
    {
        if ($this->isSaveGridParams()) {
            $limit = $this->getGirdLimit();
            $this->setDefaultLimit($limit);

            $sort = $this->getGirdSort();
            $this->setDefaultSort($sort);

            $dir = $this->getGirdDir();
            $this->setDefaultDir($dir);

            $filter = $this->getGirdFilter();
            $this->setDefaultFilter($filter);
        }
    }

    protected function saveGirdParams()
    {
        if ($this->isSaveGridParams()) {
            $params = $this->getSavedGridParams();
            $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();
            $params[$adminId]['limit'] = (int)$this->getParam($this->getVarNameLimit(), $this->_defaultLimit);
            $params[$adminId]['sort'] = $this->getParam($this->getVarNameSort(), $this->_defaultSort);
            $params[$adminId]['dir'] = $this->getParam($this->getVarNameDir(), $this->_defaultDir);
            $params[$adminId]['filter'] = $this->getParam($this->getVarNameFilter(), $this->_defaultFilter);

            Mage::getModel('core/config')->saveConfig(self::XPATH_GRID_SAVED_LIMIT, serialize($params));
        }
    }

    protected function getGirdLimit()
    {
        $params = $this->getSavedGridParams();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();

        return isset($params[$adminId]['limit']) ? $params[$adminId]['limit'] : $this->_defaultLimit;
    }

    protected function getGirdSort()
    {
        $params = $this->getSavedGridParams();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();

        return isset($params[$adminId]['sort']) ? $params[$adminId]['sort'] : $this->_defaultSort;
    }

    protected function getGirdDir()
    {
        $params = $this->getSavedGridParams();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();

        return isset($params[$adminId]['dir']) ? $params[$adminId]['dir'] : $this->_defaultDir;
    }

    protected function getGirdFilter()
    {
        $params = $this->getSavedGridParams();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();

        return isset($params[$adminId]['filter']) ? $params[$adminId]['filter'] : $this->_defaultFilter;
    }

    protected function getSavedGridParams()
    {
        if ($this->gridParams == null) {
            $params = Mage::getStoreConfig(self::XPATH_GRID_SAVED_LIMIT);
            $params = unserialize($params);
            $this->gridParams = empty($params) || !is_array($params) ? array() : $params;
        }

        return $this->gridParams;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        if (!Mage::helper('iwd_ordergrid')->isEnabled()) {
            return parent::_prepareCollection();
        }

        $filter = $this->prepareFilters();

        try {
            $collection = Mage::getResourceModel("sales/order_grid_collection");
            $collection = Mage::getModel('iwd_ordergrid/order_grid')->prepareCollection($filter, $collection);

            $this->setCollection($collection);
            Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        } catch (Exception $e) {
            Mage::log($e->getMessage());

            $session = Mage::getSingleton('adminhtml/session');
            $session->unsetData($this->getId().$this->getVarNameFilter());
            $session->unsetData($this->getId().$this->getVarNameSort());
            $this->setDefaultFilter(array());
            $this->getRequest()->setParam($this->getVarNameFilter(), null);
            $this->setDefaultSort(null);
            $this->getRequest()->setParam($this->getVarNameSort(), null);

            $collection = Mage::getResourceModel("sales/order_grid_collection");
            $collection = Mage::getModel('iwd_ordergrid/order_grid')->prepareCollection($filter, $collection);
            $this->setCollection($collection);
            Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
            Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred during filtering or sorting. Filter and sort were reset.'));
        }

        $this->saveGirdParams();

        $this->getOrderGridTotals();

        return $this;
    }

    public function getOrderGridTotals()
    {
        /**
         * @var $totals IWD_OrderGrid_Model_Order_Totals
         */
        $totals = Mage::getModel('iwd_ordergrid/order_totals');

        $collection = $this->getCollection();
        $totals->prepareTotals($collection);
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        if (!Mage::helper('iwd_ordergrid')->isEnabled()) {
            return parent::_prepareColumns();
        }

        $selectedColumns = null;
        if (!Mage::helper("iwd_ordergrid")->isEnabled()) {
            $selectedColumns = array(
                'increment_id',
                'store_id',
                'created_at',
                'billing_name',
                'shipping_name',
                'base_grand_total',
                'grand_total',
                'status',
                'action'
            );
        }

        $helper = Mage::helper('iwd_ordergrid');

        $grid = Mage::getModel('iwd_ordergrid/order_grid')->prepareColumns($this, $selectedColumns);
        $grid = Mage::getModel('iwd_ordergrid/order_grid')->addHiddenColumnWithStatus($grid);

		# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused `Mage_Rss` module": https://github.com/thehcginstitute-com/m1/issues/368
        $grid->addExportType('*/*/exportCsv', $helper->__('CSV'));
        $grid->addExportType('*/*/exportExcel', $helper->__('Excel XML'));
        $grid->sortColumnsByOrder();

        return $grid;
    }

    /**
     * @return array|mixed
     */
    protected function prepareFilters()
    {
        $filter = $this->getParam($this->getVarNameFilter(), null);

        if (is_null($filter)) {
            $filter = $this->_defaultFilter;
        }

        if (is_string($filter)) {
            $filter = $this->helper('adminhtml')->prepareFilterString($filter);
        }

        return $filter;
    }

    /**
     * @param $row
     * @return bool|string
     */
    public function getRowUrl($row)
    {
        if (!Mage::helper('iwd_ordergrid')->isEnabled()) {
            return parent::getRowUrl($row);
        }

        if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
            return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
        }

        return false;
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        if (!Mage::helper('iwd_ordergrid')->isEnabled()) {
            return parent::getGridUrl();
        }

        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!Mage::helper('iwd_ordergrid')->isEnabled()) {
            return parent::_toHtml();
        }

        return parent::_toHtml() . $this->getJsInitScripts();
    }

    /**
     * @return string
     */
    protected function getJsInitScripts()
    {
        return $this->_getChildHtml('iwd_om.order.grid.jsinit');
    }
}
