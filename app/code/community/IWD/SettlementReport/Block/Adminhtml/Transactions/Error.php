<?php

/**
 * Class IWD_SettlementReport_Block_Adminhtml_Transactions_Error
 */
class IWD_SettlementReport_Block_Adminhtml_Transactions_Error extends Mage_Adminhtml_Block_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('iwd/settlement_report/error.phtml');
    }
}
