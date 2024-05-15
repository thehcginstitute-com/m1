<?php
/**
 * 2015-02-07
 * 2020-08-13
 * 		df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * 		df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * 2024-05-16 "Port `df_csv_pretty()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/614
 * @see df_csv()
 * @used-by df_assert_in()
 * @used-by df_csv_pretty_quote()
 * @used-by df_oro_headers()
 * @used-by df_style_inline_hide()
 * @used-by dfe_modules_log()
 * @param string|string[] $a
 */
function df_csv_pretty(...$a):string {return implode(', ', dfa_flatten($a));}