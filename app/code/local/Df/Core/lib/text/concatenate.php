<?php
/**
 * 2016-10-17
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by vendor/cabinetsbay/catalog/view/frontend/templates/category/view.phtml (https://github.com/cabinetsbay/catalog/issues/18)
 * @param string|string[] ...$a
 */
function df_c(...$a):string {return implode(dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_ccc()
 * @used-by df_js_data()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @param string|string[] ...$a
 */
function df_cc(string $glue, ...$a):string {return implode($glue, dfa_flatten($a));}

/**
 * 2016-08-13
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm)
 * @param string|string[] ...$a
 */
function df_cc_br(...$a):string {return df_ccc("<br>", dfa_flatten($a));}

/**
 * 2016-08-10
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_block_output()
 * @used-by df_cli_cmd()
 * @used-by df_log_l()
 * @used-by dfe_modules_info()
 * @used-by app/code/WeltPixel/QuickCart/view/frontend/templates/checkout/cart/item/price/sidebar.phtml (https://github.com/cabinetsbay/site/issues/145)
 * @used-by vendor/cabinetsbay/core/view/frontend/templates/Magento/Tax/item/price/unit.phtml (https://github.com/cabinetsbay/site/issues/143)
 * @param string|string[] ...$a
 */
function df_cc_s(...$a):string {return df_ccc(' ', dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_cc()
 * @used-by df_asset_name()
 * @used-by df_cc_br()
 * @used-by df_cc_method()
 * @used-by df_cc_n()
 * @used-by df_cc_s()
 * @used-by df_fe_init()
 * @used-by df_log_l()
 * @used-by df_oqi_s()
 * @used-by df_report_prefix()
 * @used-by df_sentry()
 * @used-by df_url_bp()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @param string|string[] ...$a
 */
function df_ccc(string $glue, ...$a):string {return implode($glue, df_clean(dfa_flatten($a)));}