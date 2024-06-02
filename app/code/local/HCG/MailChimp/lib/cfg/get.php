<?php
/**
 * 2024-05-08 "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * 2024-06-02 https://3v4l.org/akQm0#tabs
 * @used-by Ebizmarts_MailChimp_Helper_Data::createMergeFields()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::_p()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::set()
 * @return array(string => array(string => string)
 */
function hcg_mc_cfg_fields():array {return hcg_mc_h()->unserialize(df_cfg('mailchimp/general/map_fields'));}