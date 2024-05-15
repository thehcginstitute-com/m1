<?php
/**
 * 2015-02-07
 * 2020-08-13
 * 		df_csv(['aaa', 'bbb', 'ccc']) → 'aaa,bbb,ccc'
 * 		df_csv_pretty(['aaa', 'bbb']) → 'aaa, bbb, ccc'
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * @see df_csv()
 * @used-by dfe_modules_log()
 * @used-by \Df\Framework\Validator\Currency::message()
 * @used-by \Df\Sentry\Client::send()
 * @used-by \Dfe\Moip\P\Reg::ga()
 * @used-by \Dfe\Sift\Payload\OQI::p()
 * @param string|string[] $a
 */
function df_csv_pretty(...$a):string {return implode(', ', dfa_flatten($a));}