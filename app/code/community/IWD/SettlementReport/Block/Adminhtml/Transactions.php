<?php

/**
 * Class IWD_SettlementReport_Block_Adminhtml_Transactions
 */
class IWD_SettlementReport_Block_Adminhtml_Transactions extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $this->_blockGroup = 'iwd_settlementreport';
        $this->_controller = 'adminhtml_transactions';
        $this->_headerText = Mage::helper('iwd_settlementreport')->__('Authorize.Net Settlement Report');

        parent::__construct();

        $this->_removeButton('add');
        $this->_addButton('update', array(
            'label' => $this->__('Refresh Now'),
            'onclick' => 'showLoadingMask(); setLocation(\'' . $this->getUrl('*/*/update') . '\')',
            'class' => 'add',
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'store_switcher',
            $this->getLayout()->createBlock('adminhtml/store_switcher', 'store_switcher')->setUseConfirm(false)
        );

        return parent::_prepareLayout();
    }
}