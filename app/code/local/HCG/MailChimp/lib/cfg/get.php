<?php
/**
 * 2024-05-08 "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * 2024-06-02 https://3v4l.org/akQm0#tabs
 *		{
 *			"_1468601283719_719": {"mailchimp": "WEBSITE", "magento": "1"},
 *			"_1468609069544_544": {"mailchimp": "STOREID", "magento": "2"},
 *			"_1469026825907_907": {"mailchimp": "STORECODE", "magento": "3"},
 *			"_1469027411717_717": {"mailchimp": "PREFIX", "magento": "4"},
 *			"_1469027418285_285": {"mailchimp": "FNAME", "magento": "5"},
 *			"_1469027422918_918": {"mailchimp": "MNAME", "magento": "6"},
 *			"_1469027429502_502": {"mailchimp": "LNAME", "magento": "7"},
 *			"_1469027434574_574": {"mailchimp": "SUFFIX", "magento": "8"},
 *			"_1469027444231_231": {"mailchimp": "EMAIL", "magento": "9"},
 *			"_1469027462887_887": {"mailchimp": "DOB", "magento": "11"},
 *			"_1469027468903_903": {"mailchimp": "BILLING", "magento": "13"},
 *			"_1469027475632_632": {"mailchimp": "SHIPPING", "magento": "14"},
 *			"_1469027480560_560": {"mailchimp": "TAX", "magento": "15"},
 *			"_1469027486920_920": {"mailchimp": "CONFIRMED", "magento": "16"},
 *			"_1469027496512_512": {"mailchimp": "CREATEDAT", "magento": "17"},
 *			"_1469027502720_720": {"mailchimp": "GENDER", "magento": "18"},
 *			"_1469027508616_616": {"mailchimp": "DISGRPCHG", "magento": "35"},
 *			"_1472845935735_735": {"mailchimp": "BCOMPANY", "magento": "billing_company"},
 *			"_1472846546252_252": {"mailchimp": "BCOUNTRY", "magento": "billing_country"},
 *			"_1472846569989_989": {"mailchimp": "BTELEPHONE", "magento": "billing_telephone"},
 *			"_1472846572949_949": {"mailchimp": "BZIPCODE", "magento": "billing_zipcode"},
 *			"_1472846578861_861": {"mailchimp": "SCOMPANY", "magento": "shipping_company"},
 *			"_1472846584014_14": {"mailchimp": "SCOUNTRY", "magento": "shipping_country"},
 *			"_1472846587534_534": {"mailchimp": "STELEPHONE", "magento": "shipping_telephone"},
 *			"_1472846591374_374": {"mailchimp": "SZIPCODE", "magento": "shipping_zipcode"}
 *		}
 * @used-by Ebizmarts_MailChimp_Helper_Data::createMergeFields()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::_p()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::set()
 * @return array(string => array(string => string)
 */
function hcg_mc_cfg_fields():array {return hcg_mc_h()->unserialize(df_cfg('mailchimp/general/map_fields'));}
