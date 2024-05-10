<?php
use Ebizmarts_MailChimp_Model_Config as C;
/**
 * 2024-05-08
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Helper_Data::createMergeFields()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::_p()
 */
function hcg_mc_cfg_fields() {return hcg_mc_h()->unserialize(df_cfg('mailchimp/general/map_fields'));}