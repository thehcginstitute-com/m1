<?php

/**
 * Class IWD_SettlementReport_Model_Observer
 */
class IWD_SettlementReport_Model_Observer
{
    /**
     * @param $observer
     */
    public function checkRequiredModules($observer)
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                $cache = Mage::app()->getCache();
                if ($cache->load("iwd_settlementreport") === false) {
                    $message = 'Important: Please setup IWD_ALL in order to finish
                        <strong>IWD Settlement Report</strong> installation.<br />
                        Please download <a href="https://www.iwdagency.com/modules/iwd_all.tgz" target="_blank">
                        IWD_ALL</a> and setup it via Magento Connect.<br />
                        Please refer to installation <a href="" target="_blank">guide</a>';

                    Mage::getSingleton('adminhtml/session')->addNotice($message);
                    $cache->save('true', 'iwd_settlementreport', array("iwd_settlementreport"), $lifeTime = 5);
                }
            }
        }
    }

    public function emailReports()
    {
        try {
            if (Mage::getStoreConfig('iwd_settlementreport/emailing/enable')) {
                $request = Mage::helper('iwd_settlementreport')->checkApiCredentials();
                if (isset($request['error']) && $request['error'] == 0) {
                    Mage::getModel("iwd_settlementreport/transactions")->refresh(true);
                    Mage::getModel('iwd_settlementreport/email_report')->sendEmail();
                } else {
                    Mage::log("Auto-email report: " . $request['message'], null, "iwd_settlementreport.log");
                }
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, "iwd_settlementreport.log");
        }
    }

    /**
     * @param $observer
     */
    public function updateTransactionAmount($observer)
    {
        try {
            $data = $observer->getEvent()->getData('data_object')->getData();

            if (!isset($data['transaction_id'])) {
                return;
            }

            $id = $data['transaction_id'];

            if (isset($data['additional_information']) && isset($data['additional_information']['amount'])) {
                $amount = $data['additional_information']['amount'];
            } elseif (isset($data['message'])) {
                $matches = array();
                preg_match('/[0-9]+[.,][0-9]+/', $data['message'], $matches);
                $amount = isset($matches[0]) ? $matches[0] : "NULL";
            } elseif (isset($data['amount'])) {
                $amount = $data['amount'];
            } else {
                $amount = null;
            }

            if ($amount !== null) {
                $resource = Mage::getSingleton('core/resource');
                $writeConnection = $resource->getConnection('core_write');
                $table = $resource->getTableName('sales_payment_transaction');

                $query = "UPDATE {$table} SET `amount` = '{$amount}' WHERE `transaction_id` = '{$id}'";
                $writeConnection->query($query);
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'iwd_sr.log');
        }
    }
}