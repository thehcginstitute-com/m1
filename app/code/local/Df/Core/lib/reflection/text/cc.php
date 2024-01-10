<?php
/**
 * 2016-08-10 Если класс не указан, то вернёт название функции. Поэтому в качестве $a1 можно передавать `null`.
 * 2024-01-10 "Port `df_cc_method` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/163
 * @used-by df_caller_m()
 * @used-by df_rest_action()
 * @used-by \Df\Qa\Trace\Frame::method()
 * @param string|object|null|array(object|string)|array(string = string) $a1
 * @param string|null $a2 [optional]
 */
function df_cc_method($a1, $a2 = null):string {return df_ccc('::',
	$a2 ? [df_cts($a1), $a2] : (
		!isset($a1['function']) ? $a1 :
			[dfa($a1, 'class'), $a1['function']]
	)
);}