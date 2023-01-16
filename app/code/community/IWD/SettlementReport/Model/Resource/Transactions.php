<?php

/**
 * Class IWD_SettlementReport_Model_Resource_Transactions
 */
class IWD_SettlementReport_Model_Resource_Transactions extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init('iwd_settlementreport/transactions', 'id');
    }
}