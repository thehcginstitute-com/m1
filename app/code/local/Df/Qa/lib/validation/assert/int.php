<?php
use Df\Core\Exception as DFE;
use Df\Zf\Validate\StringT\IntT;
/**
 * @see df_is_int()
 * @used-by df_product_id()
 * @used-by df_rgb2hex()
 * @used-by dfa_key_int()
 * @param mixed|mixed[] $v
 * @return int|int[]
 * @throws DFE
 */
function df_int($v, bool $allowNull = true) {/** @var int|int[] $r */
	if (is_array($v)) {
		$r = df_map(__FUNCTION__, $v, $allowNull);
	}
	elseif (is_int($v)) {
		$r = $v;
	}
	elseif (is_bool($v)) {
		$r = $v ? 1 : 0;
	}
	elseif ($allowNull && df_nes($v)) {
		$r = 0;
	}
	elseif (!IntT::s()->isValid($v)) {
		df_error(IntT::s()->message());
	}
	else {
		$r = (int)$v;
	}
	return $r;
}

/**
 * 2015-04-13
 * 1) It does not validate item types (unlike @see df_int() )
 * 2) It works only with arrays.
 * 3) Keys are preserved: http://3v4l.org/NHgdK
 * @see dfa_key_int()
 * @used-by df_fetch_col_int()
 * @used-by df_products_update()
 * @return int[]
 */
function df_int_simple(array $v):array {return array_map('intval', $v);}

/**
 * @see df_is_nat()
 * @used-by df_idn()
 * @used-by df_nat0()
 * @param mixed $v
 * @throws DFE
 */
function df_nat($v, bool $allow0 = false):int {/** @var int $r */
	$r = df_int($v, $allow0);
	$allow0 ? df_assert_ge(0, $r) : df_assert_gt0($r);
	return $r;
}

/**
 * @used-by df_date_from_timestamp_14()
 * @used-by df_day_of_week_as_digit()
 * @used-by df_hour()
 * @used-by df_month()
 * @used-by df_year()
 * @used-by \Df\Qa\Failure\Error::type()
 * @param mixed $v
 * @throws DFE
 */
function df_nat0($v):int {return df_nat($v, true);}