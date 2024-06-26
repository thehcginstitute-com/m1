<?php
/**
 * 2024-04-20 "Refactor `DS . 'mailchimp'`": https://github.com/thehcginstitute-com/m1/issues/569
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::downloadresponseAction()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_getResults()
 */
function hcg_mc_batch_delete(string $id):void {df_fs_delete(hcg_mc_batches_path($id));}

/**
 * 2024-04-20 "Refactor `DS . 'mailchimp'`": https://github.com/thehcginstitute-com/m1/issues/569
 * @used-by hcg_mc_batch_delete()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_unpackBatchFile()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::processEachResponseFile()
 */
function hcg_mc_batches_path(string $p = ''):string {return df_cc_path(Mage::getBaseDir('var'), 'mailchimp', $p);}