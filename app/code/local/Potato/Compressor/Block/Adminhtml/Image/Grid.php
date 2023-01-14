<?php

class Potato_Compressor_Block_Adminhtml_Image_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('po_compressorImageGrid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('po_compressor/compressor_image_collection',
            Mage::helper('po_compressor')->getImageGalleryFiles()
        );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'path',
            array(
                'header' => $this->__('Path'),
                'width'  => 250,
                'type'   => 'text',
                'index'  => 'path',
                'sortable' => false,
                'filter_condition_callback' => array($this, 'filterPath'),
            )
        );
        return parent::_prepareColumns();
    }

    protected function filterPath($collection, $column)
    {
        $collection->addPathFilter($column->getFilter()->getValue());
        return $this;
    }

    public function getRowUrl($row)
    {
        return '';
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');
        $this->getMassactionBlock()->addItem(
            'optimization',
            array(
                'label'   => $this->__('Optimize'),
                'url'     => $this->getUrl('adminhtml/potato_compressor_image/optimize'),
            )
        );
    }

    protected function _toHtml()
    {
        return parent::_toHtml() . $this->_getBottomHtml();
    }

    protected function _getBottomHtml()
    {
        return '<script type="text/javascript">var optimizationProgressBar = new AjaxRequestProgressBar("' . $this->getUrl('adminhtml/potato_compressor_index/optimization') . '", $("po_compressorImageGrid_massaction-form"), function(){optimizationProgressBar.hideMask()}.bind(optimizationProgressBar));</script>';
    }
}