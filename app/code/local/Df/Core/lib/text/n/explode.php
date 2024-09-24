<?php
/**
 * 2018-04-24 I have added @uses trim() today.
 * @used-by df_module_enum()
 * @used-by df_parse_colon()
 * @used-by df_tab()
 * @used-by df_zf_http_last_req()
 * @used-by \Df\Core\Text\Regex::getSubjectSplitted()
 * @return string[]
 */
function df_explode_n(string $s):array {return explode("\n", df_normalize(df_trim($s)));}