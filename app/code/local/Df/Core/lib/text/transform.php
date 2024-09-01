<?php
/**
 * 2016-01-14 Эта функция умеет работать с UTF-8, в отличие от стандартной функции @see lcfirst()
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax will reject arrays: https://3v4l.org/jFdPm
 * 2024-01-11 "Port `df_lcfirst` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/196
 * @see df_ucfirst()
 * @used-by df_camel_to_underscore()
 * @used-by df_class_second_lc()
 * @used-by df_explode_class_lc()
 * @used-by df_explode_class_lc_camel()
 * @param string|string[] ...$a
 * @return string|string[]
 */
function df_lcfirst(...$a) {return df_call_a(function(string $s):string {return
	mb_strtolower(mb_substr($s, 0, 1)) . mb_substr($s, 1)
;}, $a);}