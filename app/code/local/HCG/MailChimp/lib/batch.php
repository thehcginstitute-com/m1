<?php
use Ebizmarts_MailChimp_Model_Api_Batches as B;
/**
 * 2024-04-20 "Refactor `DS . 'mailchimp'`": https://github.com/thehcginstitute-com/m1/issues/569
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::processEachResponseFile()
 */
function hcg_mc_batches_dir():string {return Mage::getBaseDir('var') . DS . 'mailchimp';}

/**
 * 2024-04-14
 * @used-by Ebizmarts_MailChimp_Helper_Migration::_migrateOrdersFrom116()
 * @used-by Ebizmarts_MailChimp_Model_Cron::syncEcommerceBatchData()
 * @used-by Ebizmarts_MailChimp_Model_Cron::syncSubscriberBatchData()
 */
function hcg_mc_batches_new():B {return Mage::getModel('mailchimp/api_batches');}