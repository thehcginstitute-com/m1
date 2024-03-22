<?php
/**
 * @category Ebizmarts
 * @package mailchimp-lib
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cron processor class
 */
class Ebizmarts_MailChimp_Model_Cron
{
    /**
     * @var Ebizmarts_MailChimp_Helper_Data
     */
    protected $_mailChimpHelper;
    /**
     * @var Ebizmarts_MailChimp_Helper_Migration
     */
    protected $_mailChimpMigrationHelper;

    function __construct()
    {
        $this->_mailChimpHelper = Mage::helper('mailchimp');
        $this->_mailChimpMigrationHelper = Mage::helper('mailchimp/migration');
    }

    function syncEcommerceBatchData()
    {
        if ($this->getMigrationHelper()->migrationFinished()) {
            Mage::getModel('mailchimp/api_batches')->handleEcommerceBatches();
        } else {
            $this->getMigrationHelper()->handleMigrationUpdates();
        }
    }

    function syncSubscriberBatchData()
    {
        Mage::getModel('mailchimp/api_batches')->handleSubscriberBatches();
    }

    function processWebhookData()
    {
        Mage::getModel('mailchimp/processWebhook')->processWebhookData();
    }

    function deleteWebhookRequests()
    {
        Mage::getModel('mailchimp/processWebhook')->deleteProcessed();
    }

    function clearEcommerceData()
    {
        Mage::getModel('mailchimp/clearEcommerce')->clearEcommerceData();
    }
    function clearBatches()
    {
        Mage::getModel('mailchimp/clearBatches')->clearBatches();
    }

    protected function getHelper($type='')
    {
        return $this->_mailChimpHelper;
    }

    protected function getMigrationHelper()
    {
        return $this->_mailChimpMigrationHelper;
    }
}
