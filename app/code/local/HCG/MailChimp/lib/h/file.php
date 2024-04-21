<?php
use Ebizmarts_MailChimp_Helper_File as H;
/**
 * 2024-04-21
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_unpackBatchFile()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::processEachResponseFile()
 */
function hcg_mc_h_file():H {return Mage::helper('mailchimp/file');}