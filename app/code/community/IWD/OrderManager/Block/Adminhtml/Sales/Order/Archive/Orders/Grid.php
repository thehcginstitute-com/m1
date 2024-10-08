<?php

class IWD_OrderManager_Block_Adminhtml_Sales_Order_Archive_Orders_Grid extends IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid
{
    function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_archive_grid');
        $this->_blockGroup = 'iwd_ordermanager';
        $this->_controller = 'adminhtml_sales_order_archive_orders';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    protected function isSaveGridParams()
    {
        return false;
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_ordermanager/archive')->getArchiveOrdersCollection();

        if (Mage::helper("iwd_ordergrid")->isEnabled()) {
            $collection = Mage::getModel('iwd_ordermanager/archive')->getArchiveOrdersCollection();
            $filter = $this->prepareFilters();
            $collection = Mage::getModel('iwd_ordergrid/order_grid')->prepareCollection($filter, $collection);
        }

        $this->setCollection($collection);
        Mage_Adminhtml_Block_Widget_Grid::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {

        if (!Mage::helper("iwd_ordergrid")->isEnabled()) {
            return parent::_prepareColumns();
        } else {
            $helper = Mage::helper('iwd_ordergrid');
            $grid = Mage::getModel('iwd_ordergrid/order_grid')->prepareColumns($this, null);
            $grid = Mage::getModel('iwd_ordergrid/order_grid')->addHiddenColumnWithStatus($grid);

			# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the unused `Mage_Rss` module": https://github.com/thehcginstitute-com/m1/issues/368
            $grid->addExportType('*/*/exportCsv', $helper->__('CSV'));
            $grid->addExportType('*/*/exportExcel', $helper->__('Excel XML'));
            $grid->sortColumnsByOrder();
            return $grid;
        }
    }


    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();

        if (Mage::getModel('iwd_ordermanager/order')->isAllowDeleteOrders()) {
            $this->getMassactionBlock()->addItem('delete', array(
                'label' => Mage::helper('iwd_ordermanager')->__('Delete Selected Order(s)'),
                'url' => $this->getUrl('*/sales_grid/delete', array('redirect' => 'sales_archive_order')),
                'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure?')
            ));
        }

        if (Mage::getModel('iwd_ordermanager/archive')->isAllowRestoreOrders()) {
            $this->getMassactionBlock()->addItem('restore', array(
                'label' => Mage::helper('iwd_ordermanager')->__('Restore Selected Order(s)'),
                'url' => $this->getUrl('*/*/restore'),
                'confirm' => Mage::helper('iwd_ordermanager')->__('Are you sure?')
            ));
        }

        $this->getMassactionBlock()->addItem('iwd_print_order', array(
            'label' => Mage::helper('iwd_ordermanager')->__('Print Order(s)'),
            'url' => Mage::helper('adminhtml')->getUrl('*/sales_orderr/pdforders', array('redirect' => 'sales_order'))
        ));

        return $this;
    }

    function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    function _toHtml()
    {
        $script = '<script type="text/javascript">
                      if(typeof(jQueryIWD) == "undefined"){if(typeof(jQuery) != "undefined") {jQueryIWD = jQuery;}} $ji = jQueryIWD;
                      if($ji("#sales_order_archive_grid").length){
                        IWD.OrderGrid.colorGridRow();
                        IWD.OrderGrid.initComplexFilterSelect();
                        IWD.OrderGrid.initCellsWithLongString();
                        IWD.OrderGrid.initGridColumnWidth();
                        if($ji.isFunction($ji.fn.stickyTableHeaders)){$ji("#sales_order_archive_grid").stickyTableHeaders();}
                      }
                 </script>';

        return parent::_toHtml() . $script;
    }
}