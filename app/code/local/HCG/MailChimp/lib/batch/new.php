<?php
use Ebizmarts_MailChimp_Model_Api_Batches as B;
/**
 * 2024-04-14
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::downloadresponseAction()
 * @used-by Ebizmarts_MailChimp_Model_Cron::syncEcommerceBatchData()
 * @used-by Ebizmarts_MailChimp_Model_Cron::syncSubscriberBatchData()
 */
function hcg_mc_batches_new():B {return Mage::getModel('mailchimp/api_batches');}