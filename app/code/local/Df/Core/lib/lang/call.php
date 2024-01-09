<?php
/**
 * 2016-01-14
 * 2019-06-05 Parent functions with multiple different arguments are not supported!
 * 2022-11-23 `callable` as an argument type is supported by PHP ≥ 5.4:
 * https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by df_camel_to_underscore()
 * @used-by df_e()
 * @used-by df_explode_camel()
 * @used-by df_html_b()
 * @used-by df_lcfirst()
 * @used-by df_link_inline()
 * @used-by df_strtolower()
 * @used-by df_strtoupper()
 * @used-by df_tab()
 * @used-by df_ucfirst()
 * @used-by df_ucwords()
 * @used-by df_underscore_to_camel()
 * @param mixed[]|mixed[][] $parentArgs
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition
 * @return mixed|mixed[]
 */
function df_call_a(callable $f, array $parentArgs, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	/**
	 * 2016-11-13 We can not use @see df_args() here
	 * 2019-06-05
	 * The parent function could be called in 3 ways:
	 * 1) With a single array argument.
	 * 2) With a single scalar (non-array) argument.
	 * 3) With multiple arguments.
	 * `1 === count($parentArgs)` in the 1st and 2nd cases.
	 *  1 <> count($parentArgs) in the 3rd case.
	 * We should return an array in the 1st and 3rd cases, and a scalar result in the 2nd case.
	 */
	if (1 === count($parentArgs)) {
		$parentArgs = $parentArgs[0]; # 2019-06-05 It is the 1st or the 2nd case: a single argument (a scalar or an array).
	}
	return
		!is_array($parentArgs) # 2019-06-05 It is the 2nd case: a single scalar (non-array) argument
		? call_user_func_array($f, array_merge($pPrepend, [$parentArgs], $pAppend))
		: df_map($f, $parentArgs, $pAppend, $pPrepend, $keyPosition
	);
}

/**
 * 2016-02-09
 * https://3v4l.org/iUQGl
 *	 function a($b) {return is_callable($b);}
 *	 a(function() {return 0;}); возвращает true
 * https://3v4l.org/MfmCj
 *	is_callable('intval') возвращает true
 * @used-by df_const()
 * @used-by df_if()
 * @used-by df_if1()
 * @used-by df_if2()
 * @used-by df_leaf()
 * @used-by dfa()
 * @param mixed|callable $v
 * @param mixed ...$a [optional]
 * @return mixed
 */
function df_call_if($v, ...$a) {return is_callable($v) && !is_string($v) && !is_array($v)
	? call_user_func_array($v, $a) : $v
;}