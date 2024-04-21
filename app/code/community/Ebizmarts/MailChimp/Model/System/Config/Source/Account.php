<?php

/**
 * MailChimp For Magento
 *
 * @category  Ebizmarts_MailChimp
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     4/29/16 3:55 PM
 * @file:     Account.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_Account
{

    /**
     * Account details storage
     *
     * @access protected
     * @var    bool|array
     */
    protected $_accountDetails = null;

    /**
     * @var Ebizmarts_MailChimp_Helper_Data
     */
    protected $_helper;

    const USERNAME_KEY = 0;
    const TOTAL_ACCOUNT_SUB_KEY = 1;
    const TOTAL_LIST_SUB_KEY = 2;
    const STORENAME_KEY = 10;
    const SYNC_LABEL_KEY = 11;
    const TOTAL_CUS_KEY = 12;
    const TOTAL_PRO_KEY = 13;
    const TOTAL_ORD_KEY = 14;
    const TOTAL_QUO_KEY = 15;
    const NO_STORE_TEXT_KEY = 20;
    const NEW_STORE_TEXT_KEY = 21;
    const STORE_MIGRATION_TEXT_KEY = 30;
    const IN_PROGRESS = 1;
    const FINISHED = 2;
    const INVALID_KEY_MSG = "--- Invalid API Key ---";
    // Initial Sync
    const SYNC_FLAG_LABEL = 0;
    // In Progress | Finished | date
    const SYNC_FLAG_STATUS = 1;

    /**
     * Ebizmarts_MailChimp_Model_System_Config_Source_Account constructor.
     *
     * @param  $params
     * @throws Exception
     */
    function __construct($params)
    {
        $mcStore = null;
        $helper = $this->_helper = $this->makeHelper();
        $scopeArray = $helper->getCurrentScope();
        $apiKey = (empty($params))
            ? $helper->getApiKey($scopeArray['scope_id'], $scopeArray['scope'])
            : $params['api_key'];
        if ($apiKey) {
            try {
                $api = $helper->getApiByKey($apiKey);
                try {
                    $this->_accountDetails = $api->getRoot()->info('account_name,total_subscribers');

                    $mcStoreId = (empty($params))
                        ? $helper->getMCStoreId($scopeArray['scope_id'], $scopeArray['scope'])
                        : $params['mailchimp_store_id'];
                    try {
                        $mcStore = (!empty($mcStoreId))
                            ? $api->getEcommerce()->getStores()->get($mcStoreId, 'list_id,name,is_syncing')
                            : array();

                        if (empty($mcStore)) {
                            $this->_accountDetails['store_exists'] = false;
                        }
                    } catch (MailChimp_Error $e) {
                        if ($helper->isEcomSyncDataEnabled($scopeArray['scope_id'], $scopeArray['scope'])) {
                            $message = $helper->__(
                                'It seems your Mailchimp store was deleted. '
                                . 'Please create a new one and associate it in order to get your Ecommerce data synced.'
                            );
                            Mage::getSingleton('adminhtml/session')->addWarning($message);
                        }

                        $this->_accountDetails['store_exists'] = false;
                    }

                    try {
                        $listId = (isset($mcStore['list_id']) && $mcStore['list_id']) ? $mcStore['list_id'] : null;


                        if ($listId) {
                            $listData = $api->getLists()->getLists($listId, 'stats');
                            $this->_accountDetails['list_subscribers'] = $listData['stats']['member_count'];
                        }

                        if (!isset($this->_accountDetails['store_exists'])) {
                            $ecommerceApi = $api->getEcommerce();
                            $this->_accountDetails['store_exists'] = true;
                            $this->_accountDetails['store_name'] = $mcStore['name'];
                            //Keep both values for backward compatibility
                            $this->_accountDetails['store_sync_flag'] = $mcStore['is_syncing'];
                            $this->_accountDetails['store_sync_date'] = $this->getDateSync($mcStoreId);
                            $totalCustomers = $ecommerceApi->getCustomers()->getAll($mcStoreId, 'total_items');
                            $this->_accountDetails['total_customers'] = $totalCustomers['total_items'];
                            $totalProducts = $ecommerceApi->getProducts()->getAll($mcStoreId, 'total_items');
                            $this->_accountDetails['total_products'] = $totalProducts['total_items'];
                            $totalOrders = $ecommerceApi->getOrders()->getAll($mcStoreId, 'total_items');
                            $this->_accountDetails['total_orders'] = $totalOrders['total_items'];
                            $totalCarts = $ecommerceApi->getCarts()->getAll($mcStoreId, 'total_items');
                            $this->_accountDetails['total_carts'] = $totalCarts['total_items'];
                        }
                    } catch (MailChimp_Error $e) {
                        $helper->logError($e->getMessage());
                    }
                } catch (MailChimp_Error $e) {
                    $this->_accountDetails = self::INVALID_KEY_MSG;
                    $helper->logError($e->getMessage());
                }
            } catch (Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
                $this->_accountDetails = self::INVALID_KEY_MSG;
                $helper->logError($e->getMessage());
            }
        } else {
            $this->_accountDetails = self::INVALID_KEY_MSG;
        }
    }

    /**
     * Return data if API key is entered
     *
     * @return array
     */
    function toOptionArray()
    {
        $helper = $this->_helper;
        $scopeArray = $helper->getCurrentScope();
        if (is_array($this->_accountDetails)) {
            $totalAccountSubscribersText = $helper->__('Total Account Subscribers:');
            $totalAccountSubscribers = $totalAccountSubscribersText . ' ' . $this->_accountDetails['total_subscribers'];
            $totalListSubscribers = null;
            if (isset($this->_accountDetails['list_subscribers'])) {
                $totalListSubscribersText = $helper->__('Total Audience Subscribers:');
                $totalListSubscribers = $totalListSubscribersText . ' ' . $this->_accountDetails['list_subscribers'];
            }

            $username = $helper->__('Username:') . ' ' . $this->_accountDetails['account_name'];
            $returnArray = array(
                array('value' => self::USERNAME_KEY, 'label' => $username),
                array('value' => self::TOTAL_ACCOUNT_SUB_KEY, 'label' => $totalAccountSubscribers)
            );
            if ($totalListSubscribers) {
                $returnArray[] = array('value' => self::TOTAL_LIST_SUB_KEY, 'label' => $totalListSubscribers);
            }

            if ($this->_accountDetails['store_exists']) {
                $totalCustomersText = $helper->__('  Total Customers:');
                $totalCustomers = $totalCustomersText . ' ' . $this->_accountDetails['total_customers'];
                $totalProductsText = $helper->__('  Total Products:');
                $totalProducts = $totalProductsText . ' ' . $this->_accountDetails['total_products'];
                $totalOrdersText = $helper->__('  Total Orders:');
                $totalOrders = $totalOrdersText . ' ' . $this->_accountDetails['total_orders'];
                $totalCartsText = $helper->__('  Total Carts:');
                $totalCarts = $totalCartsText . ' ' . $this->_accountDetails['total_carts'];
                $title = $helper->__('Ecommerce Data uploaded to Mailchimp store ')
                    . $this->_accountDetails['store_name']
                    . ':';
                if ($this->_accountDetails['store_sync_flag']
                    && !$this->_accountDetails['store_sync_date']
                    && !$helper->getResendEnabled($scopeArray['scope_id'], $scopeArray['scope'])
                ) {
                    $syncValue = self::IN_PROGRESS;
                } else {
                    $syncData = $this->_accountDetails['store_sync_date'];
                    if ($helper->validateDate($syncData)) {
                        $syncValue = $syncData;
                    } else {
                        $syncValue = self::FINISHED;
                    }
                }

                $syncLabel = $helper->__('Initial sync') . ': ' . $syncValue;
                $returnArray = array_merge(
                    $returnArray,
                    array(
                        array('value' => self::STORENAME_KEY, 'label' => $title),
                        array('value' => self::SYNC_LABEL_KEY, 'label' => $syncLabel),
                        array('value' => self::TOTAL_CUS_KEY, 'label' => $totalCustomers),
                        array('value' => self::TOTAL_PRO_KEY, 'label' => $totalProducts),
                        array('value' => self::TOTAL_ORD_KEY, 'label' => $totalOrders),
                        array('value' => self::TOTAL_QUO_KEY, 'label' => $totalCarts)
                    )
                );
            } elseif ($helper->isEcomSyncDataEnabled($scopeArray['scope_id'], $scopeArray['scope'], true)) {
                $noStoreText = $helper->__(
                    'No MailChimp store was configured for this scope, parent scopes might be '
                    . 'sending data for this store anyways.'
                );
                $returnArray = array_merge(
                    $returnArray,
                    array(
                        array('value' => self::NO_STORE_TEXT_KEY, 'label' => $noStoreText)
                    )
                );
            }
            return $returnArray;
        } elseif (!$this->_accountDetails) {
            return array(array('value' => '', 'label' => $helper->__('--- Enter your API KEY first ---')));
        } else {
            return array(array('value' => '', 'label' => $helper->__($this->_accountDetails)));
        }
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function makeHelper() {return hcg_mc_h();}

    /**
     * @param $mailchimpStoreId
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function getDateSync($mailchimpStoreId)
    {
        $date = $this->makeHelper()->getConfigValueForScope(
            Ebizmarts_MailChimp_Model_Config::ECOMMERCE_SYNC_DATE . "_$mailchimpStoreId",
            0,
            'default'
        );
        return $date;
    }
}
