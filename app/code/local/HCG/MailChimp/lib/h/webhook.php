<?php
use Ebizmarts_MailChimp_Helper_Curl as H;
/**
 * 2024-04-21
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()
 */
function hcg_mc_h_webhook():H {return Mage::helper('mailchimp/webhook');}