<?php
/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     6/10/16 12:38 AM
 * @file:     Grid.php
 */
class Ebizmarts_MailChimp_Block_Adminhtml_Mailchimperrors_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    function __construct()
    {
        parent::__construct();
        $this->setId('mailchimp_mailchimperrors_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mailchimp/mailchimperrors')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'title',
            array(
            'header' => 'Title',
            'index' => 'title',
            'sortable' => true
            )
        );
        $this->addColumn(
            'status',
            array(
            'header' => 'Status',
            'index' => 'status',
            'width' => '100px',
            'sortable' => true
            )
        );
        $this->addColumn(
            'regtype',
            array(
            'header' => 'Reg Type',
            'index' => 'regtype',
            'width' => '100px',
            'sortable' => true
            )
        );
        $this->addColumn(
            'store_id',
            array(
                'header' => 'Store Id',
                'index' => 'store_id',
                'sortable' => false
            )
        );
        $this->addColumn(
            'errors',
            array(
                'header' => 'Error',
                'index'  => 'errors',
                'sortable' => false
            )
        );
        $this->addColumn(
            'batch_id',
            array(
                'header' => 'Batch ID',
                'index'  => 'batch_id',
                'sortable' => false
            )
        );
        $this->addColumn(
            'action_donwload',
            array(
                'header'   => 'Download Response',
                'width'    => 15,
                'sortable' => false,
                'filter'   => false,
                'type'     => 'action',
                'getter'   => 'getId',
                'actions'  => array(
                    array(
                        'url'     => array('base'=> '*/*/downloadresponse'),
                        'caption' => 'Download',
                        'field'   => 'id'
                    ),
                )
            )
        );
        $this->addColumn(
            'original_id',
            array(
            'header' => 'Original',
            'index' => 'original_id',
            'sortable' => false,
            'renderer' => 'mailchimp/adminhtml_mailchimperrors_link'
            )
        );
        $this->addColumn(
            'created_at',
            array(
                'header' => 'Created At',
                'index' => 'created_at',
                'sortable' => true,
            )
        );

        return parent::_prepareColumns();
    }

    function getRowUrl($row)
    {
        return false;
    }
    function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}
