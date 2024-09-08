<?php

class IWD_OrderGrid_Block_Adminhtml_Customer_Order_Recent extends Mage_Adminhtml_Block_Customer_Edit_Tab_View_Orders
{
    /**
     * {@inheritdoc}
     */
    function __construct()
    {
        parent::__construct();

        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $this->setColumnFilters(
                array('iwd_multiselect' => 'iwd_ordergrid/adminhtml_widget_grid_column_filter_multiselect')
            )->setColumnRenderers(
                array('iwd_multiselect' => 'adminhtml/widget_grid_column_renderer_options')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _preparePage()
    {
        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $selectedColumns = Mage::getStoreConfig(IWD_OrderGrid_Helper_Data::CONFIG_XPATH_CUSTOMER_RESENT_ORDERS_COUNT);
            $this->getCollection()
                ->setPageSize($selectedColumns)
                ->setCurPage(1);
        } else {
            parent::_preparePage();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $collection = Mage::getModel('iwd_ordergrid/customer_order')->getRecentOrdersCollectionForCurrentCustomer();
            $this->setCollection($collection);
            return Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();
        }

        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $selectedColumnsGrid = Mage::getModel('iwd_ordergrid/customer_order')->getSelectedColumnsForRecentOrderGrid();

            $grid = Mage::getModel('iwd_ordergrid/order_grid')->prepareColumns($this, $selectedColumnsGrid);
            Mage::getModel('iwd_ordergrid/order_grid')->addHiddenColumnWithStatus($grid);
            Mage::getModel('iwd_ordergrid/order_grid')->addReorderColumn($grid);

            $this->sortColumnsByOrder();
            return $this;
        }

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    function _toHtml()
    {
        $script = '';

        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $script = '<script type="text/javascript">
                    if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}}
                    $ji = jQueryIWD;
                    if($ji("#customer_view_orders_grid_table").length) {
                        IWD.OrderGrid.colorGridRow();
                        IWD.OrderGrid.initComplexFilterSelect();
                        IWD.OrderGrid.initCellsWithLongString();
                        IWD.OrderGrid.initGridColumnWidth();
                        if($ji.isFunction($ji.fn.stickyTableHeaders)){
                            $ji("#customer_view_orders_grid_table").stickyTableHeaders();
                        }
                    }
                 </script>';
        }

        return parent::_toHtml() . $script;
    }
}
