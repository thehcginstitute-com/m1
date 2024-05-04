<?php
use Ebizmarts_MailChimp_Helper_Webhook as H;
/**
 * 2024-05-04
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_addSubscriberData()
 */
function hcg_mc_h_webhook():H {return Mage::helper('mailchimp/webhook');}