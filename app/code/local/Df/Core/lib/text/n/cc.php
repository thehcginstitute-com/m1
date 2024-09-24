<?php
/**
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @used-by df_api_rr_failed()
 * @used-by df_error_create()
 * @used-by df_fe_init()
 * @used-by df_kv()
 * @used-by df_log_l()
 * @used-by df_tag_list()
 * @used-by df_tab()
 * @used-by df_xml_g()
 * @used-by df_xml_prettify()
 * @used-by df_zf_http_last_req()
 * @used-by dfp_error_message()
 * @used-by Df\Core\Html\Tag::content()
 * @used-by Df\Core\Text\Regex::getSubjectReportPart()
 * @used-by Df\Qa\Dumper::dumpArrayElements()
 * @used-by Df\Qa\Method::raiseErrorParam()
 * @used-by Df\Qa\Method::raiseErrorResult()
 * @used-by Df\Qa\Method::raiseErrorVariable()
 * @param string|array(string) ...$a
 */
function df_cc_n(...$a):string {return df_ccc("\n", ...$a);}