<?php
use Df\Core\Exception as DFE;
use Df\Zf\Validate\StringT\FloatT;

/**
 * @used-by df_float_positive()
 * @used-by dff_chop0()
 * @used-by dfp_last2()
 * @param mixed|mixed[] $v
 * @return float|float[]
 * @throws DFE
 */
function df_float($v, bool $allowNull = true) {/** @var int|int[] $r */
	if (is_array($v)) {
		$r = df_map(__FUNCTION__, $v, $allowNull);
	}
	elseif (is_float($v)) {
		$r = $v;
	}
	elseif (is_int($v)) {
		$r = floatval($v);
	}
	elseif ($allowNull && df_nes($v)) {
		$r = 0.0;
	}
	else {
		$valueIsString = is_string($v); /** @var bool $valueIsString */
		static $cache = []; /** @var array(string => float) $cache */
		if ($valueIsString && isset($cache[$v])) {
			$r = $cache[$v];
		}
		elseif (!FloatT::s()->isValid($v)) {
			/**
			 * Обратите внимание, что мы намеренно используем @uses df_error(),
			 * а не @see df_error().
			 * Например, модуль доставки «Деловые Линии»
			 * не оповещает разработчика только об исключительных ситуациях
			 * класса @see Exception,
			 * которые порождаются функцией @see df_error().
			 * О сбоях преобразования типов надо оповещать разработчика.
			 */
			df_error(FloatT::s()->message());
		}
		else {
			df_assert($valueIsString);
			/**
			 * Хотя @see Zend_Validate_Float вполне допускает строки в формате «60,15»
			 * при установке надлежащей локали (например, ru_RU),
			 * @uses floatval для строки «60,15» вернёт значение «60», обрубив дробную часть.
			 * Поэтому заменяем десятичный разделитель на точку.
			 */
			# Обратите внимание, что 368.0 === floatval('368.')
			$r = floatval(str_replace(',', '.', $v));
			$cache[$v] = $r;
		}
	}
	return $r;
}

/**
 * @used-by df_float_positive0()
 * @param mixed $v
 * @return float|null
 * @throws DFE
 */
function df_float_positive($v, bool $allow0 = false, bool $throw = true) {/** @var float|null $r */
	if (!$throw) {
		try {$r = df_float_positive($v, $allow0, true);}
		# 2023-11-27
		# PHP < 8 requires a variable declaration:
 		# «unexpected ')', expecting '|' or variable (T_VARIABLE)
		# in vendor/mage2pro/core/Qa/lib/validation\assert/float.php on line 73»:
		# https://github.com/mage2pro/core/issues/337
		catch (\Throwable $t) {$r = null;}
	}
	else {
		$r = df_float($v, $allow0);
		$allow0 ? df_assert_ge(0, $r) : df_assert_gt0($r);
	}
	return $r;
}

/**
 * 2021-03-06 @deprecated It is unused.
 * @param mixed $v
 * @throws DFE
 */
function df_float_positive0($v):float {return df_float_positive($v, true);}