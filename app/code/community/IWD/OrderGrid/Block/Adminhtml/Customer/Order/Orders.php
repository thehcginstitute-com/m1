<?php

class IWD_OrderGrid_Block_Adminhtml_Customer_Order_Orders extends Mage_Adminhtml_Block_Customer_Edit_Tab_Orders
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();

        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            if (Mage::helper('iwd_ordergrid')->isEnabled()) {
                $this->setColumnFilters(
                    array('iwd_multiselect' => 'iwd_ordergrid/adminhtml_widget_grid_column_filter_multiselect')
                )->setColumnRenderers(
                    array('iwd_multiselect' => 'adminhtml/widget_grid_column_renderer_options')
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $collection = Mage::getModel('iwd_ordergrid/customer_order')->getOrdersCollectionForCurrentCustomer();
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
            $selectedColumnsGrid = Mage::getModel('iwd_ordergrid/customer_order')->getSelectedColumnsForOrderGrid();
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
    public function _toHtml()
    {
        $script = '';

        if (Mage::helper('iwd_ordergrid')->isEnabled()) {
            $script = '<script type="text/javascript">
                    if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}}
                    $ji = jQueryIWD;
                    if($ji("#customer_orders_grid_table").length) {
                        IWD.OrderGrid.colorGridRow();
                        IWD.OrderGrid.initComplexFilterSelect();
                        IWD.OrderGrid.initCellsWithLongString();
                        IWD.OrderGrid.initGridColumnWidth();
                        if($ji.isFunction($ji.fn.stickyTableHeaders)){
                            $ji("#customer_orders_grid_table").stickyTableHeaders();
                        }
                    }
                 </script>';
        }

        return parent::_toHtml() . $script;
    }
}
