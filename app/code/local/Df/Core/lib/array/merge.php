<?php
use Exception as E;

/**
 * 2015-02-18
 * По смыслу функция @see df_extend() аналогична методу @see \Magento\Framework\Simplexml\Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Magento\Framework\Simplexml\Element::extend(),
 * @see df_extend() сливает не XML, а ассоциативные массивы.
 *
 * Обратите внимание, что вместо @see df_extend() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * Но и стандартные рекурсивные функции тоже не подходят:
 *
 * 1)
 * array_merge_recursive(array('width' => 180), array('width' => 200))
 * вернёт: array(array('width' => array(180, 200)))
 * https://php.net/manual/function.array-merge-recursive.php
 * Наша функция df_extend(array('width' => 180), array('width' => 200))
 * вернёт array('width' => 200)
 *
 * 2)
 * array_replace_recursive(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт: array('x' => array('С', 'B'))
 * https://php.net/manual/function.array-replace-recursive.php
 * Наша функция df_extend(array('x' => array('A', 'B')), array('x' => 'C'))
 * вернёт array('x' => 'C')
 *
 * 2018-11-13
 * 1) df_extend(
 *		['TBCBank' => ['1111' => ['a' => 'b']]]
 *		,['TBCBank' => ['2222' => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => ['1111' => ['a' => 'b'], '2222' => ['c' => 'd']]
 * 2) df_extend(
 *		['TBCBank' => [1111 => ['a' => 'b']]]
 *		,['TBCBank' => [2222 => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => [1111 => ['a' => 'b'], 2222 => ['c' => 'd']]
 *
 * @used-by df_extend()
 * @used-by df_log_l()
 * @param array(string => mixed) $defaults
 * @param array(string => mixed) $newValues
 * @return array(string => mixed)
 * @throws E
 * @return array
 */
function df_extend(array $defaults, array $newValues) {/** @var array(string => mixed) $r */
	# Здесь ошибочно было бы $r = [], потому что если ключ отсутствует в $newValues, то тогда он не попадёт в $r.
	$r = $defaults;
	foreach ($newValues as $key => $newValue) {/** @var int|string $key */ /** @var mixed $newValue */
		$defaultValue = dfa($defaults, $key); /** @var mixed $defaultValue */
		if (!is_array($defaultValue)) {
			# 2016-08-23 unset добавил сегодня.
			if (is_null($newValue)) {
				unset($r[$key]);
			}
			else {
				$r[$key] = $newValue;
			}
		}
		elseif (is_array($newValue)) {
			$r[$key] = df_extend($defaultValue, $newValue);
		}
		elseif (is_null($newValue)) {
			unset($r[$key]);
		}
		else {
			# Если значение по умолчанию является массивом, а новое значение не является массивом,
			# то это наверняка говорит об ошибке программиста.
			df_error();
		}
	}
	return $r;
}

/**
 * 2017-10-28
 * Plain `array_merge($r, $b)` works wronly, if $b contains contains SOME numeric-string keys like "99":
 * https://github.com/mage2pro/core/issues/40#issuecomment-340139933
 * https://stackoverflow.com/a/5929671
 * @used-by dfa_select_ordered()
 * @param array(string|int => mixed) $r
 * @param array(string|int => mixed) $b
 * @return array(string|int => mixed)
 */
function dfa_merge_numeric(array $r, array $b) {
	foreach ($b as $k => $v) {
		$r[$k] = $v;
	}
	return $r;
}

/**
 * 2015-02-18
 * 1) По смыслу функция @see dfa_merge_r() аналогична методу @see \Magento\Framework\Simplexml\Element::extend()
 * и предназначена для слияния настроечных опций,
 * только, в отличие от @see \Magento\Framework\Simplexml\Element::extend(),
 * @see dfa_merge_r() сливает не XML, а ассоциативные массивы.
 * 3) Вместо @see dfa_merge_r() нельзя использовать ни
 * @see array_replace_recursive(), ни @see array_merge_recursive(),
 * ни тем более @see array_replace() и @see array_merge()
 * 3.1) Нерекурсивные аналоги отметаются сразу, потому что не способны сливать вложенные структуры.
 * 3.2) Но и стандартные рекурсивные функции тоже не подходят:
 * 		3.2.1) array_merge_recursive(['width' => 180], ['width' => 200]) вернёт: ['width' => [180, 200]]
 * 		https://php.net/manual/function.array-merge-recursive.php
 * 		3.2.2) Наша функция dfa_merge_r(['width' => 180], ['width' => 200]) вернёт ['width' => 200]
 * 		3.2.3) array_replace_recursive(['x' => ['A', 'B']], ['x' => 'C']) вернёт: ['x' => ['С', 'B']]
 * 		https://php.net/manual/function.array-replace-recursive.php
 * 		3.2.4) Наша функция dfa_merge_r(['x' => ['A', 'B']], ['x' => 'C']) вернёт ['x' => 'C']
 * 2018-11-13
 * 1) dfa_merge_r(
 *		['TBCBank' => ['1111' => ['a' => 'b']]]
 *		,['TBCBank' => ['2222' => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => ['1111' => ['a' => 'b'], '2222' => ['c' => 'd']]
 * 2) dfa_merge_r(
 *		['TBCBank' => [1111 => ['a' => 'b']]]
 *		,['TBCBank' => [2222 => ['c' => 'd']]]
 * )
 * is: 'TBCBank' => [1111 => ['a' => 'b'], 2222 => ['c' => 'd']]
 * 2024-01-11 "Port `dfa_merge_r` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/179
 * @used-by df_ci_add()
 * @used-by dfa_merge_r()
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_oi_add()
 * @used-by df_sentry()
 * @param array(string => mixed) $old
 * @param array(string => mixed) $new
 * @return array(string => mixed)
 */
function dfa_merge_r(array $old, array $new):array {
	# Здесь ошибочно было бы $r = [], потому что если ключ отсутствует в $new, то тогда он не попадёт в $r.
	$r = $old; /** @var array(string => mixed) $r */
	foreach ($new as $k => $newV) {/** @var int|string $k */ /** @var mixed $newV */
		if (is_null($newV))	{
			unset($r[$k]); ## 2016-08-23
		}
		else {
			/** 2023-07-29 I have removed @see dfa() to improve speed. */
			$oldV = isset($old[$k]) ? $old[$k] : null; /** @var mixed $oldV */
			if (is_array($oldV) && is_array($newV)) {
				$r[$k] = dfa_merge_r($oldV, $newV);
			}
			else {
				$r[$k] = $newV;
			}
		}
	}
	return $r;
}