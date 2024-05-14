<?php
use Df\Core\Exception as DFE;
use Df\Zf\Validate\StringT\IntT;
/**
 * 2024-05-14 "Port `df_int()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/605
 * @see df_is_int()
 * @used-by df_product_id()
 * @used-by df_rgb2hex()
 * @used-by dfa_key_int()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::_p() (https://github.com/cabinetsbay/site/issues/589)
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