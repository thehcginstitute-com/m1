<?php
/**
 * @used-by df_con_hier_suf_ta()
 * @used-by df_explode_xpath()
 * @used-by df_fe_init()
 * @used-by df_find()
 * @used-by df_map()
 * @param mixed|mixed[] $v
 * @return array
 */
function df_array($v) {return is_array($v) ? $v : [$v];}

/**
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * 2024-05-14 "Port `df_index()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/600
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * https://3v4l.org/AOWmO
 * @used-by df_mvars()
 * @param string|Closure $k
 * @param Traversable|array(int|string => _DO) $a
 */
function df_index($k, iterable $a):array {return array_combine(df_column($a, $k), df_ita($a));}

/**
 * 2016-03-25 http://stackoverflow.com/a/1320156
 * @used-by df_action_is()
 * @used-by df_c()
 * @used-by df_cc()
 * @used-by df_cc_br()
 * @used-by df_cc_class()
 * @used-by df_cc_class_uc()
 * @used-by df_cc_path()
 * @used-by df_cc_path_t()
 * @used-by df_cc_s()
 * @used-by df_ccc()
 * @used-by df_class_replace_last()
 * @used-by df_contains()
 * @used-by df_csv_pretty()
 * @used-by df_explode_class_camel()
 * @used-by df_explode_xpath()
 * @used-by df_mail()
 * @used-by df_string_clean()
 * @used-by dfa_unpack()
 * @return array
 */
function dfa_flatten(array $a) {
	$r = []; /** @var mixed[] $r */
	array_walk_recursive($a, function($a) use(&$r):void {$r[]= $a;});
	return $r;
}

/**
 * 2016-09-02
 * @see dfa_deep_unset()
 * @uses array_flip() корректно работает с пустыми массивами.
 * @param array(string => mixed) $a
 * @param string[] $keys
 * @return array(string => mixed)
 */
function dfa_unset(array $a, array $keys) {return array_diff_key($a, array_flip($keys));}

/**
 * 2021-01-29
 * @used-by df_gender_s() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function df_tr($v, array $map) {return dfa($map, $v, $v);}