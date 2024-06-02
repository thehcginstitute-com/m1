<?php
use Df\Core\Exception as DFE;
use Df\Zf\Validate\StringT\IntT;
/**
 * 2024-05-14 "Port `df_int()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/605
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
 * 2024-06-02 "Port `df_int_simple()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/623
 * 1) It does not validate item types (unlike @see df_int() )
 * 2) It works only with arrays.
 * 3) Keys are preserved: http://3v4l.org/NHgdK
 * @see dfa_key_int()
 * @used-by df_fetch_col_int()
 * @used-by df_products_update()
 * @return int[]
 */
function df_int_simple(array $v):array {return array_map('intval', $v);}