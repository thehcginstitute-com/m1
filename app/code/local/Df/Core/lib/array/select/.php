<?php
/**
 * 2021-01-29
 * @used-by df_error_create()
 * @used-by df_region_name()
 * @used-by dfa_try()
 * @used-by hcg_mc_cfg_scope() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_handleCookie() (https://github.com/thehcginstitute-com/m1/issues/530)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 * @used-by HCG\MailChimp\Batch\ProcessEachResponseFile::p()
 * @used-by HCG\MailChimp\Cfg::scopeByPathV() (https://github.com/thehcginstitute-com/m1/issues/641)
 * @used-by HCG\MailChimp\Tags::_p()
 * @used-by HCG\MailChimp\Tags::mcByMG() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::gender() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by IWD_OrderManager_Adminhtml_Sales_AddressController::format() (https://github.com/thehcginstitute-com/m1/issues/533)
 * @used-by IWD_OrderManager_Model_Order_Edit::updateOrderItems() (https://github.com/thehcginstitute-com/m1/issues/533)
 * @used-by IWD_OrderManager_Model_Order_Items::editItems() (https://github.com/thehcginstitute-com/m1/issues/533)
 * @used-by app/design/frontend/base/default/template/richpanel/head.phtml
 * @param array(int|string => mixed) $a
 * @param string|string[]|int|null $k
 * @param mixed $d
 * @return mixed|null|array(string => mixed)
 */
function dfa(array $a, $k, $d = null) {return
	# 2016-02-13
	# Нельзя здесь писать `return df_if2(isset($array[$k]), $array[$k], $d);`, потому что получим «Notice: Undefined index».
	# 2016-08-07
	# В Closure мы можем безнаказанно передавать параметры, даже если closure их не поддерживает https://3v4l.org/9Sf7n
	df_nes($k) ? $a : (is_array($k)
		/**
		 * 2022-11-26
		 * Added `!$k`.
		 * @see df_arg() relies on it if its argument is an empty array:
		 *		df_arg([]) => []
		 *		dfa($a, df_arg([])) => $a
		 * https://3v4l.org/C09vn
		 */
		? (!$k ? $a : dfa_select_ordered($a, $k))
		: (isset($a[$k]) ? $a[$k] : (df_contains($k, '/') ? dfa_deep($a, $k, $d) : df_call_if($d, $k)))
	)
;}