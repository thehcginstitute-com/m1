<?php
     
    class Pma_Importer_Block_Adminhtml_Importer_Grid extends Mage_Adminhtml_Block_Widget_Grid
    {
        public function __construct()
        {
            parent::__construct();
            $this->setId('importerGrid');
            $this->setDefaultSort('increment_id');
            $this->setDefaultDir('DESC');
            $this->setSaveParametersInSession(true);
            $this->setUseAjax(true);
        }
     
       protected function _getCollectionClass()
        {
            return 'sales/order_grid_collection';
        }
    
        protected function _prepareCollection()
        {
            $collection = Mage::getResourceModel($this->_getCollectionClass());
            $this->setCollection($collection);
            return parent::_prepareCollection();
        }
    
        protected function _prepareColumns()
        {
    
            $this->addColumn('real_order_id', array(
                'header'=> Mage::helper('sales')->__('Order #'),
                'width' => '80px',
                'type'  => 'text',
                'index' => 'increment_id',
            ));
    
            if (!Mage::app()->isSingleStoreMode()) {
                $this->addColumn('store_id', array(
                    'header'    => Mage::helper('sales')->__('Purchased From (Store)'),
                    'index'     => 'store_id',
                    'type'      => 'store',
                    'store_view'=> true,
                    'display_deleted' => true,
                ));
            }
    
            $this->addColumn('created_at', array(
                'header' => Mage::helper('sales')->__('Purchased On'),
                'index' => 'created_at',
                'type' => 'datetime',
                'width' => '100px',
            ));
    
            $this->addColumn('billing_name', array(
                'header' => Mage::helper('sales')->__('Bill to Name'),
                'index' => 'billing_name',
            ));
    
            $this->addColumn('shipping_name', array(
                'header' => Mage::helper('sales')->__('Ship to Name'),
                'index' => 'shipping_name',
            ));
    
            $this->addColumn('base_grand_total', array(
                'header' => Mage::helper('sales')->__('G.T. (Base)'),
                'index' => 'base_grand_total',
                'type'  => 'currency',
                'currency' => 'base_currency_code',
            ));
    
            $this->addColumn('grand_total', array(
                'header' => Mage::helper('sales')->__('G.T. (Purchased)'),
                'index' => 'grand_total',
                'type'  => 'currency',
                'currency' => 'order_currency_code',
            ));
    
            $this->addColumn('status', array(
                'header' => Mage::helper('sales')->__('Status'),
                'index' => 'status',
                'type'  => 'options',
                'width' => '70px',
                'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
            ));
    
          return parent::_prepareColumns();
        }
    
        protected function _prepareMassaction()
        {
            $this->setMassactionIdField('entity_id');
            $this->getMassactionBlock()->setFormFieldName('order_ids');
            $this->getMassactionBlock()->setUseSelectAll(false);
    
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/cancel')) {
                $this->getMassactionBlock()->addItem('cancel_order', array(
                     'label'=> Mage::helper('sales')->__('Cancel'),
                     'url'  => $this->getUrl('*/sales_order/massCancel'),
                ));
            }
    
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/hold')) {
                $this->getMassactionBlock()->addItem('hold_order', array(
                     'label'=> Mage::helper('sales')->__('Hold'),
                     'url'  => $this->getUrl('*/sales_order/massHold'),
                ));
            }
    
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/unhold')) {
                $this->getMassactionBlock()->addItem('unhold_order', array(
                     'label'=> Mage::helper('sales')->__('Unhold'),
                     'url'  => $this->getUrl('*/sales_order/massUnhold'),
                ));
            }
    
            $this->getMassactionBlock()->addItem('pdfinvoices_order', array(
                 'label'=> Mage::helper('sales')->__('Print Invoices'),
                 'url'  => $this->getUrl('*/sales_order/pdfinvoices'),
            ));
    
            $this->getMassactionBlock()->addItem('pdfshipments_order', array(
                 'label'=> Mage::helper('sales')->__('Print Packingslips'),
                 'url'  => $this->getUrl('*/sales_order/pdfshipments'),
            ));
    
            $this->getMassactionBlock()->addItem('pdfcreditmemos_order', array(
                 'label'=> Mage::helper('sales')->__('Print Credit Memos'),
                 'url'  => $this->getUrl('*/sales_order/pdfcreditmemos'),
            ));
    
            $this->getMassactionBlock()->addItem('pdfdocs_order', array(
                 'label'=> Mage::helper('sales')->__('Print All'),
                 'url'  => $this->getUrl('*/sales_order/pdfdocs'),
            ));
    
			# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the shipping labels feature because it is unused":
			# https://github.com/thehcginstitute-com/m1/issues/375
    
            return $this;
        }
    
        public function getRowUrl($row)
        {
            if (Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/view')) {
                return $this->getUrl('*/sales_order/view', array('order_id' => $row->getId()));
            }
            return false;
        }
    
        public function getGridUrl()
        {
            return $this->getUrl('*/*/grid', array('_current'=>true));
        }
     
     
    }