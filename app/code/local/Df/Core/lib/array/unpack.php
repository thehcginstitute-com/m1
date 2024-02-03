<?php
/**
 * 2015-12-25
 * Этот загадочный метод призван заменить код вида: `is_array($a) ? $a : func_get_args()`.
 * Теперь можно писать так: df_args(func_get_args()).
 * 2024-02-03 "Port `df_args()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/348
 * @see df_arg()
 * @see dfa_unpack()
 * @used-by df_clean()
 * @used-by df_clean_keys()
 * @used-by df_csv()
 * @used-by df_csv_pretty_quote()
 * @used-by df_format()
 * @used-by dfa_combine_self()
 * @used-by dfa_unset()
 * @see dfa_unpack()
 */
function df_args(array $a):array {return !$a || !is_array($a[0]) ? $a : $a[0];}
