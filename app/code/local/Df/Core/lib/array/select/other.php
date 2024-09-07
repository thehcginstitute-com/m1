<?php
use Varien_Object as _DO;

/**
 * 2020-01-29
 * 2024-05-14 "Port `dfad()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/603
 * @used-by df_call()
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return _DO|mixed
 */
function dfad(_DO $o, $k = '', $d = null) {return df_nes($k) ? $o : dfa(df_gd($o), $k, $d);}

/**
 * 2015-02-08
 * 2020-01-29
 * 1) It returns a subset of $a with $k keys in the same order as in $k.
 * 2) Normally, you should use @see dfa() instead because it is shorter and calls dfa_select_ordered() internally.
 * @used-by dfa()
 * @param array(string => string)|T $a
 * @param string[] $k
 * @return array(string => string)
 */
function dfa_select_ordered($a, array $k) {
	$resultKeys = array_fill_keys($k, null); /** @var array(string => null) $resultKeys */
	/**
	 * 2017-10-28
	 * During the last 2.5 years, I had the following code here:
	 * 		array_merge($resultKeys, df_ita($source))
	 * It worked wronly, if $source contained SOME numeric-string keys like "99":
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
	 *
	 * «A key may be either an integer or a string.
	 * If a key is the standard representation of an integer, it will be interpreted as such
	 * (i.e. "8" will be interpreted as 8, while "08" will be interpreted as "08").»
	 * https://php.net/manual/language.types.array.php
	 *
	 * «If, however, the arrays contain numeric keys, the later value will not overwrite the original value,
	 * but will be appended.
	 * Values in the input array with numeric keys will be renumbered
	 * with incrementing keys starting from zero in the result array.»
	 * https://php.net/manual/function.array-merge.php
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140297
	 * `df_ita($source) + $resultKeys` does not solve the problem,
	 * because the result keys are ordered in the `$source` order, not in the `$resultKeys` order:
	 * https://github.com/mage2pro/core/issues/40#issuecomment-340140766
	 * @var array(string => string) $resultWithGarbage
	 */
	$resultWithGarbage = dfa_merge_numeric($resultKeys, df_ita($a));
	return array_intersect_key($resultWithGarbage, $resultKeys);
}

/**
 * 2019-01-28
 * @used-by Glew_Service_ModuleController::_initRequest()
 * 2024-03-05
 * 1) https://3v4l.org/C3qrh
 * 2) The previous code (`dfa_seq`): https://github.com/mage2pro/core/blob/10.6.9/Core/lib/array/main.php#L214-L231
 * @param array(int|string => mixed) $a
 * @return mixed|null
 */
function dfa_try(array $a, string ...$k) {return df_first(dfa($a, $k));}