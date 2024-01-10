<?php
/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * 2024-01-10 "Port `df_cc` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/159
 * @see df_ccc()
 * @used-by df_js_data()
 * @used-by \Df\Qa\Trace\Formatter::p()
 * @param string|string[] $a
 */
function df_cc(string $glue, ...$a):string {return implode($glue, dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_api_rr_failed()
 * @used-by df_fe_init()
 * @used-by df_kv()
 * @used-by df_log_l()
 * @used-by df_tag_list()
 * @used-by df_tab_multiline()
 * @used-by df_xml_g()
 * @used-by df_xml_prettify()
 * @used-by df_zf_http_last_req()
 * @used-by dfp_error_message()
 * @param string|string[] $a
 * @return string
 */
function df_cc_n(...$a) {return df_ccc("\n", ...$a);}

/**
 * 2015-12-01 Отныне всегда используем / вместо DIRECTORY_SEPARATOR.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_config_e()
 * @used-by df_db_credentials()
 * @used-by df_fs_etc()
 * @used-by df_img_resize()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @param string|string[] $a
 * @return string
 */
function df_cc_path(...$a) {return df_ccc('/', dfa_flatten($a));}

/**
 * 2016-08-10
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @used-by df_block_output()
 * @used-by df_cli_cmd()
 * @used-by df_log_l()
 * @used-by dfe_modules_info()
 * @param string|string[] $a
 * @return string
 */
function df_cc_s(...$a) {return df_ccc(' ', dfa_flatten($a));}

/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @see df_cc()
 * @used-by df_asset_name()
 * @used-by df_cc_br()
 * @used-by df_cc_method()
 * @used-by df_cc_n()
 * @used-by df_cc_path()
 * @used-by df_cc_s()
 * @used-by df_fe_init()
 * @used-by df_log_l()
 * @used-by df_oqi_s()
 * @used-by df_report_prefix()
 * @used-by df_url_bp()
 * @param string string
 * @param string|string[] $a
 * @return string
 */
function df_ccc($glue, ...$a) {return implode($glue, df_clean(dfa_flatten($a)));}