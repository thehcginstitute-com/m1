<?php
use Closure as F;
use Exception as E;
/**
 * 2017-03-28 If the function is called from a closure, then it will go up through the stask until it leaves all closures.
 * @used-by df_caller_f()
 * @used-by df_caller_m()
 * @used-by df_log_l()
 * @param E|int|null|array(array(string => string|int)) $p [optional]
 * @return array(string => string|int)
 */
function df_caller_entry($p = 0, F $predicate = null) {
	/**
	 * 2018-04-24
	 * I do not understand why did I use `2 + $offset` here before.
	 * Maybe the @uses array_slice() was included in the backtrace in previous PHP versions (e.g. PHP 5.6)?
	 * array_slice() is not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
	 * (I have checked it in the both XDebug enabled and disabled cases).
	 * 2019-01-14
	 * It seems that we need `2 + $offset` because the stack contains:
	 * 1) the current function: df_caller_entry
	 * 2) the function who calls df_caller_entry: df_caller_f, df_caller_m, or \Df\Framework\Log\Dispatcher::handle
	 * 3) the function who calls df_caller_f, df_caller_m, or \Df\Framework\Log\Dispatcher::handle: it should be the result.
	 * So the offset is 2.
	 */
	$bt = df_bt(df_bt_inc($p, 2)); /** @var array(int => array(string => mixed)) $bt */
	while ($r = array_shift($bt)) {/** @var array(string => string|int)|null $r */
		$f = $r['function']; /** @var string $f */
		# 2017-03-28
		# Надо использовать именно df_contains(),
		# потому что PHP 7 возвращает просто строку «{closure}», а PHP 5.6 и HHVM — «A::{closure}»: https://3v4l.org/lHmqk
		# 2020-09-24 I added "unknown" to evaluate expressions in IntelliJ IDEA's with xDebug.
		if (!df_contains($f, '{closure}') && !in_array($f, ['dfc', 'dfcf', 'unknown']) && (!$predicate || $predicate($r))) {
			break;
		}
	}
	return df_eta($r); /** 2021-10-05 @uses array_shift() returns `null` for an empty array */
}