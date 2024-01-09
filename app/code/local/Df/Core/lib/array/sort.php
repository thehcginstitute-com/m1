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