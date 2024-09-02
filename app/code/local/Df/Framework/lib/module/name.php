<?php
/**
 * «Dfe\AllPay\W\Handler» => «Dfe_AllPay»
 * 2016-10-26
 * The function correctly handles class names without a namespace and with the `_` character:
 * 		«A\B\C» => «A_B»
 * 		«A_B» => «A_B»
 * 		«A» => A»
 * 		https://3v4l.org/Jstvc
 * 2017-01-27
 * $c could be:
 * 		1) a module name: «A_B»
 * 		2) a class name: «A\B\C».
 * 		3) an object: it comes down to the case 2 via @see get_class()
 * 		4) `null`: it comes down to the case 1 with the «Df_Core» module name.
 * 2024-01-11 "Port `df_module_name` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/181
 * @used-by df_asset_name()
 * @used-by df_caller_module()
 * @used-by df_con()
 * @used-by df_fe_init()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_log()
 * @used-by df_module_dir()
 * @used-by df_module_enabled()
 * @used-by df_module_name_c()
 * @used-by df_package()
 * @used-by df_route()
 * @used-by df_sentry_module()
 * @used-by df_widget()
 * @used-by dfpm_title()
 * @used-by \Df\Core\Session::__construct()
 * @param string|object|null $c [optional]
 */
function df_module_name($c = null, string $d = '_'):string {return dfcf(
	function(string $c, string $d):string {return implode($d, array_slice(df_explode_class($c), 0, 2));}
	,[$c ? df_cts($c) : 'Df\Core', $d]
);}

/**
 * 2017-01-04
 * 		$c could be:
 * 		1) a module name. E.g.: «A_B».
 * 		2) a class name. E.g.: «A\B\C».
 * 		3) an object. It will be treated as case 2 after @see get_class()
 * 2024-01-11 "Port `df_module_name_c` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/194
 * @used-by df_module_name_lc()
 * @used-by dfp_report()
 * @used-by dfs()
 * @used-by dfs_con()
 * @param string|object|null $c [optional]
 */
function df_module_name_c($c = null):string {return df_module_name($c, '\\');}

/**
 * 2016-02-16 «Dfe\CheckoutCom\Method» => «dfe_checkout_com»
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2017-10-03
 * $c could be:
 * 		1) a module name. E.g.: «A_B».
 * 		2) a class name. E.g.: «A\B\C».
 * 		3) an object. It will be treated as case 2 after @see get_class()
 * 2024-01-11 "Port `df_module_name_lc` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/193
 * @see df_cts_lc_camel()
 * @used-by df_report_prefix()
 * @param string|object $c
 */
function df_module_name_lc($c, string $d = '_'):string {return implode($d, df_explode_class_lc_camel(df_module_name_c($c)));}