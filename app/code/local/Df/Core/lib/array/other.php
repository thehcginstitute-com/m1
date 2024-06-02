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
 * 2018-04-24
 * 2024-04-08
 * 1) I added the `is_null($k)` case: https://github.com/thehcginstitute-com/m1/issues/551
 * 2) 2024-04-08 "Port `dfa_group()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/552
 * @used-by IWD_OrderGrid_Model_Order_Grid::prepareGridColumns() (https://github.com/thehcginstitute-com/m1/issues/551)
 * @param array(int|string => mixed) $a
 * @param string|int|null $k
 * @return array(int|string => array(int|string => mixed))
 */
function dfa_group(array $a, $k = null):array {
	$r = []; /** @var array(int|string => array(int|string => mixed)) $r */
	if (is_null($k)) {
		foreach ($a as $k => $index) { /** @var int|string $index */
			if (!isset($r[$index])) {
				$r[$index] = [];
			}
			$r[$index][] = $k;
		}
	}
	else {
		$isInt = is_int($k); /** @var bool $isInt */
		foreach ($a as $v) { /** @var mixed $v */
			$index = $v[$k]; /** @var string $index */
			if (!isset($r[$index])) {
				$r[$index] = [];
			}
			unset($v[$k]);
			$r[$index][] = 1 === count($v) ? df_first($v) : (!$isInt ? $v : array_values($v));
		}
	}
	return $r;
}

/**
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * 2024-05-14 "Port `df_index()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/600
 * @used-by df_mvars()
 * @param string|Closure $k
 * @param Traversable|array(int|string => _DO) $a
 */
function df_index($k, $a):array {return array_combine(df_column($a, $k), df_ita($a));}

/**
 * 2015-02-11
 * Эта функция отличается от @see iterator_to_array() тем, что допускает в качестве параметра
 * не только @see \Traversable, но и массив.
 * @param \Traversable|array $t
 * @return array
 */
function df_ita($t) {return is_array($t) ? $t : iterator_to_array($t);}

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