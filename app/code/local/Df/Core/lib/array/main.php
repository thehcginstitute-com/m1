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
 * 2017-02-18 [array|callable, array|callable] => [array, callable]
 * @used-by df_filter_f()
 * @used-by df_find()
 * @used-by df_map()
 * @used-by dfak_transform()
 * @param array|callable|Traversable $a
 * @param array|callable|Traversable $b
 * @return array(int|string => mixed)
 */
function dfaf($a, $b):array {
	# 2020-02-15
	# "A variable is expected to be a traversable or an array, but actually it is a «object»":
	# https://github.com/tradefurniturecompany/site/issues/36
	$ca = is_callable($a); /** @var bool $ca */
	$cb = is_callable($b); /** @var bool $ca */
	if (!$ca || !$cb) {
		df_assert($ca || $cb);
		$r = $ca ? [df_assert_traversable($b), $a] : [df_assert_traversable($a), $b];
	}
	else {
		$ta = is_iterable($a); /** @var bool $ta */
		$tb = is_iterable($b); /** @var bool $tb */
		if ($ta && $tb) {
			df_error('dfaf(): both arguments are callable and traversable: %s and %s.', df_type($a), df_type($b));
		}
		df_assert($ta || $tb);
		$r = $ta ? [$a, $b] : [$b, $a];
	}
	return $r;
}

/**
 * 2021-01-29
 * @used-by \IWD_OrderManager_Helper_Data::CheckTableEngine()
 * @param array $a
 * @return mixed|null
 */
function df_first(array $a) {return !$a ? null : reset($a);}

/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see \Traversable, но и массив.
 * @param \Traversable|array $t
 * @return array
 */
function df_ita($t) {return is_array($t) ? $t : iterator_to_array($t);}

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see df_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see df_last() смещения указателя не происходит,
 * потому что в @see df_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 *	$result = end($array);
 *	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @param mixed[] $array
 * @return mixed|null
 */
function df_last(array $array) {return !$array ? null : end($array);}

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
	array_walk_recursive($a, function($a) use(&$r) {$r[]= $a;});
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
 * @param int|string $v
 * @param array(int|string => mixed) $map
 * @return int|string|mixed
 */
function df_tr($v, array $map) {return dfa($map, $v, $v);}