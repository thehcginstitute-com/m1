<?php
/**
 * 2019-09-08
 * 2024-01-11 "Port `DF_N` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/190
 * @used-by df_n_get()
 * @used-by df_n_set()
 * @used-by df_prop()
 * @used-by df_prop_k()
 * @used-by \Df\Core\Json::bSort()
 */
const DF_N = 'df-null';

/**
 * 2019-04-05
 * 2019-09-08 Now it supports static properties.
 * 2023-12-31 For static properties, pass `null` as the first argument, e.g.:
 * 		@see \Df\Core\Json::bSort()
 * 2024-01-11 "Port `df_prop` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/189
 * @used-by \Df\Core\Json::bSort()
 * @see dfc()
 * @param object|null|ArrayAccess $o
 * @param mixed|string $v [optional]
 * @param string|mixed|null $d [optional]
 * @return mixed|object|ArrayAccess|null
 */
function df_prop($o, $v = DF_N, $d = null, string $type = '') {/** @var object|mixed|null $r */
	/**
	 * 2019-09-08
	 * 1) My 1st solution was comparing $v with `null`,
	 * but it is wrong because it fails for a code like `$object->property(null)`.
	 * 2) My 2nd solution was using @see func_num_args():
	 * «How to tell if optional parameter in PHP method/function was set or not?»
	 * https://stackoverflow.com/a/3471863
	 * It is wrong because the $v argument is alwaus passed to df_prop()
	 */
	$isGet = DF_N === $v; /** @vae bool $isGet */
	if ('int' === $d) {
		$type = $d; $d = null;
	}
	/** @var string $k */
	if (!is_null($o)) {
		$r = df_prop_k($o, df_caller_f(), $v, $d);
	}
	else {# 2019-09-08 A static call.
		$k = df_caller_m();
		# 2023-08-04
		# «dfa(): Argument #1 ($a) must be of type array, null given,
		# called in vendor/mage2pro/core/Core/lib/lang/prop.php on line 109»: https://github.com/mage2pro/core/issues/314
		static $s = []; /** @var array(string => mixed) $s */
		if ($isGet) {
			$r = dfa($s, $k, $d);
		}
		else {
			$s[$k] = $v;
			$r = null;
		}
	}
	return $isGet && 'int' === $type ? intval($r) : $r;
}