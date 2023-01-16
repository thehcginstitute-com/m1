<?php

/**
 * Class IWD_SettlementReport_Model_Authorize_AuthorizeNet
 */
class IWD_SettlementReport_Model_Authorize_AuthorizeNet extends Mage_Core_Model_Abstract
{
    /**
     * Live url
     */
    const LIVE_URL = "https://api.authorize.net/xml/v1/request.api";

    /**
     * Sandbox url
     */
    const SANDBOX_URL = "https://apitest.authorize.net/xml/v1/request.api";

    /**
     * @var string
     */
    protected $_apiLogin;

    /**
     * @var string
     */
    protected $_transactionKey;

    /**
     * @var string
     */
    protected $_postString;

    /**
     * @var bool
     */
    protected $_sandbox = true;

    /**
     * @var string
     */
    protected $_xml;

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        parent::_construct();
        $this->initConnection();
    }

    /**
     * @param null $store
     * @return $this
     */
    public function initConnection($store = null)
    {
        $standardAuth = Mage::getStoreConfig('iwd_settlementreport/connection/standard', $store);

        if ($standardAuth) {
            $this->_apiLogin = Mage::getStoreConfig('payment/authorizenet/login', $store);
            $this->_transactionKey = Mage::getStoreConfig('payment/authorizenet/trans_key', $store);
            $this->_sandbox = Mage::getStoreConfig('payment/authorizenet/test', $store);
        } else {
            $login = Mage::getStoreConfig('iwd_settlementreport/connection/login', $store);
            $transKey = Mage::getStoreConfig('iwd_settlementreport/connection/trans_key', $store);
            $this->_apiLogin = Mage::helper('core')->decrypt($login);
            $this->_transactionKey = Mage::helper('core')->decrypt($transKey);
            $this->_sandbox = Mage::getStoreConfig('iwd_settlementreport/connection/test', $store);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostString()
    {
        return $this->_postString;
    }

    /**
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    protected function _sendRequest()
    {
        $this->_setPostString();
        $postUrl = $this->_getPostUrl();
        $curlRequest = curl_init($postUrl);
        curl_setopt($curlRequest, CURLOPT_POSTFIELDS, $this->_postString);
        curl_setopt($curlRequest, CURLOPT_HEADER, 0);
        curl_setopt($curlRequest, CURLOPT_TIMEOUT, 45);
        curl_setopt($curlRequest, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlRequest, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($curlRequest, CURLOPT_SSL_VERIFYPEER, false);

        if (preg_match('/xml/', $postUrl)) {
            curl_setopt($curlRequest, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        }

        $response = curl_exec($curlRequest);

        curl_close($curlRequest);

        return $this->_handleResponse($response);
    }

    /**
     * @param bool $includeStatistics
     * @param bool $firstSettlementDate
     * @param bool $lastSettlementDate
     * @param bool $utc
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    public function getSettledBatchList(
        $includeStatistics = false,
        $firstSettlementDate = false,
        $lastSettlementDate = false,
        $utc = true
    ) {
        $utc = ($utc ? "Z" : "");
        $this->_constructXml("getSettledBatchListRequest");
        ($includeStatistics ? $this->_xml->addChild("includeStatistics", $includeStatistics) : null);
        ($firstSettlementDate ? $this->_xml->addChild("firstSettlementDate", $firstSettlementDate . $utc) : null);
        ($lastSettlementDate ? $this->_xml->addChild("lastSettlementDate", $lastSettlementDate . $utc) : null);

        return $this->_sendRequest();
    }

    /**
     * @param bool $month
     * @param bool $year
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    public function getSettledBatchListForMonth($month = false, $year = false)
    {
        $month = ($month ? $month : date('m'));
        $year = ($year ? $year : date('Y'));
        $firstSettlementDate = substr(date('c', mktime(0, 0, 0, $month, 1, $year)), 0, -6);
        $lastSettlementDate = substr(date('c', mktime(0, 0, 0, $month + 1, 0, $year)), 0, -6);
        return $this->getSettledBatchList(true, $firstSettlementDate, $lastSettlementDate);
    }

    /**
     * @param $batchId
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    public function getTransactionList($batchId)
    {
        $this->_constructXml("getTransactionListRequest");
        $this->_xml->addChild("batchId", $batchId);
        return $this->_sendRequest();
    }

    /**
     * @param bool $month
     * @param bool $day
     * @param bool $year
     * @return array
     */
    public function getTransactionsForDay($month = false, $day = false, $year = false)
    {
        $transactions = array();
        $month = ($month ? $month : date('m'));
        $day = ($day ? $day : date('d'));
        $year = ($year ? $year : date('Y'));
        $firstSettlementDate = substr(date('c', mktime(0, 0, 0, (int)$month, (int)$day, (int)$year)), 0, -6);
        $lastSettlementDate = substr(date('c', mktime(0, 0, 0, (int)$month, (int)$day, (int)$year)), 0, -6);
        $response = $this->getSettledBatchList(true, $firstSettlementDate, $lastSettlementDate);
        $batches = $response->xpath("batchList/batch");
        foreach ($batches as $batch) {
            $batchId = (string)$batch->batchId;
            $request = new AuthorizeNetTD;
            $tranList = $request->getTransactionList($batchId);
            $transactions = array_merge($transactions, $tranList->xpath("transactions/transaction"));
        }

        return $transactions;
    }

    /**
     * @param $transId
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    public function getTransactionDetails($transId)
    {
        $this->_constructXml("getTransactionDetailsRequest");
        $this->_xml->addChild("transId", $transId);
        return $this->_sendRequest();
    }

    /**
     * @param $batchId
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    public function getBatchStatistics($batchId)
    {
        $this->_constructXml("getBatchStatisticsRequest");
        $this->_xml->addChild("batchId", $batchId);
        return $this->_sendRequest();
    }

    /**
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    public function getUnsettledTransactionList()
    {
        $this->_constructXml("getUnsettledTransactionListRequest");
        return $this->_sendRequest();
    }

    /**
     * @return string
     */
    protected function _getPostUrl()
    {
        return ($this->_sandbox ? self::SANDBOX_URL : self::LIVE_URL);
    }

    /**
     * @param $response
     * @return IWD_SettlementReport_Model_Authorize_Response
     */
    protected function _handleResponse($response)
    {
        return new IWD_SettlementReport_Model_Authorize_Response($response);
    }

    /**
     * Set post string
     */
    protected function _setPostString()
    {
        $this->_postString = $this->_xml->asXML();
    }

    /**
     * @param $requestType
     */
    protected function _constructXml($requestType)
    {
        $string = '<?xml version="1.0" encoding="utf-8"?><' . $requestType . '></' . $requestType . '>';

        $this->_xml = @new SimpleXMLElement($string);
        $this->_xml->addAttribute('xmlns', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd');

        $merchant = $this->_xml->addChild('merchantAuthentication');
        $merchant->addChild('name', $this->_apiLogin);
        $merchant->addChild('transactionKey', $this->_transactionKey);
    }
}
