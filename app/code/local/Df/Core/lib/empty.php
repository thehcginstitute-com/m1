<?php
/**
 * @see df_nes()
 * @used-by df_assert_sne()
 * @used-by df_desc()
 * @used-by df_leaf()
 * @used-by df_leaf_sne()
 * @used-by df_n_prepend()
 * @used-by df_param_sne()
 * @used-by df_path_is_internal()
 * @used-by df_report()
 * @used-by df_result_sne()
 * @used-by \HetNieuweWeb_CustomerNavigation_Block_Customer_Account_Navigation::renameLinkByName()
 * @param mixed $v
 * @return bool
 */
function df_es($v) {return '' === $v;}

/**
 * 2017-04-26
 * @used-by df_caller_entry()
 * @used-by df_ci_get()
 * @used-by df_fe_fc()
 * @used-by df_fetch_one()
 * @used-by df_oi_add()
 * @used-by df_oi_get()
 * @used-by df_package()
 * @used-by df_primary_key()
 * @used-by df_trd()
 * @used-by HCG\MailChimp\Batch\ProcessEachResponseFile::p() (https://github.com/thehcginstitute-com/m1/issues/572)
 * @used-by Mage_Sales_Model_Order::queueNewOrderEmail() (https://github.com/thehcginstitute-com/m1/issues/538)
 * @param mixed|null $v
 * @return array
 */
function df_eta($v) {
	if (!is_array($v)) {
		df_assert(empty($v));
		$v = [];
	}
	return $v;
}

/**
 * 2020-01-29
 * @used-by df_countries_options()
 * @used-by df_currencies_options()
 * @used-by df_customer_session_id()
 * @used-by df_img_resize()
 * @used-by df_slice()
 * @used-by dfa_chop()
 * @param mixed $v
 * @return mixed|null
 */
function df_etn($v) {return $v ?: null;}

/**
 * 2023-07-26 "Implement `df_ets()`": https://github.com/mage2pro/core/issues/280
 * 2024-01-10 "Port `df_ets` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/171
 * @used-by df_log_l()
 * @used-by df_region_name()
 * @param mixed $v
 * @return mixed|string
 */
function df_ets($v) {return $v ?: '';}

/**
 * 2024-03-23 "Port `df_ftn()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/STUB
 * @used-by df_fetch_one()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::address() (https://github.com/cabinetsbay/site/issues/589)
 * @param mixed|false $v
 * @return mixed|null
 */
function df_ftn($v) {return false === $v ? null : $v;}

/**
 * 2016-08-04
 * @see df_es()
 * @used-by dfa()
 * @used-by dfa_deep()
 * @used-by dfa_strict()
 * @used-by dfad()
 * @used-by dftr()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::customizedAttributes() (https://github.com/cabinetsbay/site/issues/589)
 * @param mixed $v
 * @return bool
 */
function df_nes($v) {return is_null($v) || '' === $v;}

/**
 * @param mixed|null $v
 * @return mixed
 */
function df_nts($v) {return !is_null($v) ? $v : '';}