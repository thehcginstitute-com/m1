<?php
use Ebizmarts_MailChimp_Model_Api_Batches as B;
/**
 * 2024-04-14
 */
function hcg_mc_batches_new():B {return Mage::getModel('mailchimp/api_batches');}