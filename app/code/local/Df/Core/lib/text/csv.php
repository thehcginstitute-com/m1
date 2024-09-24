<?php
/**
 * 2015-02-07
 * 2020-08-13
 * 		df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * 		df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_csv()
 * @used-by df_oro_get_list()
 * @param string|string[] ...$a
 */
function df_csv(...$a):string {return implode(',', df_args($a));}

/**
 * 2015-02-07
 * @used-by df_country_codes_allowed()
 * @used-by df_csv_parse_int()
 * @used-by df_days_off()
 * @used-by df_fe_fc_csv()
 * @param string|null $s
 * @return string[]
 */
function df_csv_parse($s, string $d = ','):array {return !$s ? [] : df_trim(explode($d, $s));}

/**
 * @used-by df_days_off()
 * @param string|null $s
 * @return int[]
 */
function df_csv_parse_int($s):array {return df_int(df_csv_parse($s));}

/**
 * 2015-02-07
 * 2020-08-13
 * 		df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * 		df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @see df_csv()
 * @used-by df_assert_in()
 * @used-by df_csv_pretty_quote()
 * @used-by df_oro_headers()
 * @used-by df_style_inline_hide()
 * @used-by dfe_modules_log()
 * @param string|string[] ...$a
 */
function df_csv_pretty(...$a):string {return implode(', ', dfa_flatten($a));}

/**
 * 2022-10-29 @deprecated It is unused.
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * @param string|string[] ...$a
 */
function df_csv_pretty_quote(...$a):string {return df_csv_pretty(df_quote_russian(df_args($a)));}