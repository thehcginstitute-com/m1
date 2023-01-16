<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Grid
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Types_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('orderFlagsTypes');
        $this->_blockGroup = 'iwd_orderflags';
        $this->_controller = 'adminhtml_flags_types';

        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('iwd_orderflags/flags_types')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $helper = Mage::helper('iwd_orderflags');

        $this->addColumn(
            'id',
            array(
                'header' => $helper->__('ID'),
                'align' => 'right',
                'width' => '50px',
                'index' => 'id',
                'filter_index' => 'id',
                'type' => 'number',
                'sortable' => true
            )
        );

        $this->addColumn(
            'name',
            array(
                'header' => $helper->__('Name'),
                'align' => 'left',
                'index' => 'name',
                'type' => 'text',
                'filter_index' => 'name',
                'width' => '200px',
                'sortable' => true
            )
        );

        $this->addColumn(
            'comment',
            array(
                'header' => $helper->__('Comment'),
                'align' => 'left',
                'index' => 'comment',
                'type' => 'text',
                'filter_index' => 'comment',
                'sortable' => true
            )
        );

        $this->addColumn(
            'action',
            array(
                'header' => $helper->__('Action'),
                'width' => '120',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => $helper->__('Edit'),
                        'url' => array(
                            'base' => '*/flags_types/edit',
                        ),
                        'field' => 'id'
                    )
                ),
                'column_css_class' => 'select-confirm-action',
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
            )
        );

        return parent::_prepareColumns();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('id');

        $this->getMassactionBlock()->addItem(
            'delete',
            array(
                'label' => Mage::helper('iwd_orderflags')->__('Delete'),
                'url' => $this->getUrl('*/*/massDelete'),
                'confirm' => Mage::helper('iwd_orderflags')->__('Are you sure?')
            )
        );

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
