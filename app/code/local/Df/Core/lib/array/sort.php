<?php
/**
 * 2016-01-29
 * @see df_sort()
 * @param array(int|string => mixed) $a
 * @param callable|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_ksort(array $a, $f = null) {$f ? uksort($a, $f) : ksort($a); return $a;}

/**
 * 2017-08-22
 * Note 1. For now it is never used.
 * Note 2. An alternative implementation: df_ksort($a, 'strcasecmp')
 * 2017-09-07 Be careful! If the $a array is not associative,
 * then ksort($a, SORT_FLAG_CASE|SORT_STRING) will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @see df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_ci(array $a) {ksort($a, SORT_FLAG_CASE|SORT_STRING); return $a;}

/**
 * 2017-07-05
 * 2017-08-22 From now it is never used. @see df_ksort_r_ci()
 * @param array(int|string => mixed) $a
 * @param callable|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_ksort_r(array $a, $f = null) {return df_ksort(df_map_k(function($k, $v) use($f) {return
	!is_array($v) ? $v : df_ksort_r($v, $f)
;}, $a), $f);}

/**
 * 2017-08-22
 * 2017-09-07 Be careful! If the $a array is not associative,
 * then df_ksort_r($a, 'strcasecmp') will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_sort()
 * @uses df_ksort_ci()
 * @param array(int|string => mixed) $a
 * @return array(int|string => mixed)
 */
function df_ksort_r_ci(array $a) {return !df_is_assoc($a) ? $a : df_ksort_r($a, 'strcasecmp');}

/**
 * 2016-07-18
 * 2016-08-10
 * С сегодняшнего дня я использую функцию @see df_caller_f(),
 * которая, в свою очередь, использует @debug_backtrace().
 * Это приводит к сбою: «Warning: usort(): Array was modified by the user comparison function».
 * http://stackoverflow.com/questions/3235387
 * https://bugs.php.net/bug.php?id=50688
 * По этой причине добавил собаку.
 * 2022-11-30
 * «Deprecated Functionality: Collator::__construct():
 * Passing null to parameter #1 ($locale) of type string is deprecated
 * in vendor/justuno.com/core/lib/Core/array/sort.php on line 102»:
 * https://github.com/justuno-com/core/issues/379
 * 2024-04-08
 * 1) From now on, it is locale-aware (previously, I had a separate locale-aware df_sort_l() / df_sort_names() function).
 * 2) https://3v4l.org/iV0BT
 * 3) $isGet = true in:
 * 		@used-by df_oqi_leafs()
 * 4) $l is used in:
 * 		@used-by df_oqi_leafs()
 * 2024-04-08 "Port `df_sort()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/547
 * @see df_ksort()
 * @used-by df_countries_options()
 * @used-by df_json_sort()
 * @used-by df_modules_p()
 * @used-by df_oqi_leafs()
 * @used-by df_sort_l()
 * @used-by df_zf_http_last_req()
 * @param array(int|string => mixed) $a
 * @param Closure|string|null $f [optional]
 * @return array(int|string => mixed)
 */
function df_sort(array $a, $f = null, bool $isGet = false, string $l = ''):array {
	# 2017-02-02 http://stackoverflow.com/a/7930575
	$c = new Collator($l); /** @var Collator $c */
	$isList = array_is_list($a); /** @var bool $isList */
	if (!$f) {
		$isList ? $c->sort($a) : $c->asort($a);
	}
	else {
		if ($isGet) {
			$f = function($a, $b) use($c, $f):int {return $c->compare(!$f ? $a : $f($a), !$f ? $b : $f($b));};
		}
		elseif (!$f instanceof Closure) {
			$f = function($a, $b) use($f):int {return !is_object($a) ? $a - $b : $a->$f() - $b->$f();};
		}
		/** @noinspection PhpUsageOfSilenceOperatorInspection */
		$isList ? @usort($a, $f) : @uasort($a, $f);
	}
	return $a;
}