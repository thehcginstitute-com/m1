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