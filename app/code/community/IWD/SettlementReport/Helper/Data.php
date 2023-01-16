<?php

/**
 * Class IWD_SettlementReport_Helper_Data
 */
class IWD_SettlementReport_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @var string
     */
    protected $_version = 'EE';

    /**
     * @return bool
     */
    public function isAvailableVersion()
    {
        $mage = new Mage();
        if (!is_callable(array($mage, 'getEdition'))) {
            $edition = 'Community';
        } else {
            $edition = Mage::getEdition();
        }

        unset($mage);

        if ($edition == 'Enterprise' && $this->_version == 'CE') {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isGridExport()
    {
        /**
         * @var $http Mage_Core_Helper_Http
         */
        $http = Mage::helper('core/http');
        $path = $http->getRequestUri();

        $exportCsv = (strstr($path, 'exportCsv') !== false);
        $exportExcel = (strstr($path, 'exportExcel') !== false);
        $exportForEmail = (strstr($path, 'sendreport') !== false);

        return $exportCsv || $exportExcel || $exportForEmail;
    }

    /**
     * @param null $storeId
     * @return array
     */
    public function checkApiCredentials($storeId = null)
    {
        try {
            if (!$this->isSettlementReportEnabled($storeId)) {
                Mage::throwException('Settlement report is disabled');
            }

            $this->checkCredentials($storeId);
            $this->checkAuthorizeCredentials();
        } catch (\Exception $e) {
            return array('error' => 1, 'message' => $this->__($e->getMessage()));
        }

        return array('error' => 0, 'message' => $this->__('Connected successfully.'));
    }

    /**
     * @param null $storeId
     * @return bool
     */
    protected function isSettlementReportEnabled($storeId = null)
    {
        return (bool)Mage::getStoreConfig('iwd_settlementreport/connection/enabled', $storeId);
    }

    /**
     * @param $storeId
     */
    protected function checkCredentials($storeId)
    {
        $standardAuth = Mage::getStoreConfig('iwd_settlementreport/connection/standard', $storeId);
        if ($standardAuth) {
            $active = Mage::getStoreConfig('payment/authorizenet/active', $storeId);
            if (!$active) {
                Mage::throwException('Authorize.net payment method is disabled.');
            }
        } else {
            $login = Mage::getStoreConfig('iwd_settlementreport/connection/login', $storeId);
            $transKey = Mage::getStoreConfig('iwd_settlementreport/connection/trans_key', $storeId);

            if (!$login || !$transKey) {
                Mage::throwException('Enter API credentials and save to test.');
            }
        }
    }

    protected function checkAuthorizeCredentials()
    {
        $date = substr(date('c', time()), 0, -6);
        $details = Mage::getModel('iwd_settlementreport/authorize_authorizeNet');
        $details = $details->getSettledBatchList(false, $date, $date);
        $result = (array)$details->xml->messages;

        if (!isset($result['resultCode']) || $result['resultCode'] == 'Error') {
            $message = (array)$result['message'];
            Mage::throwException($message["code"] . ": " . $message["text"]);
        }
    }
}
