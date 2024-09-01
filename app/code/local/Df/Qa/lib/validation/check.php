<?php
/**
 * 2021-03-22
 * @used-by df_assert_between()
 * @param int|string $v
 * @param int|float|null $min
 * @param int|float|null $max
 */
function df_between($v, $min, $max, bool $inclusive = true):bool {return
	$inclusive ? $v >= $min && $v <= $max : $v > $min && $v < $max
;}

/**
 * 2024-06-02
 * 1) "Implement `df_is_email()`": https://github.com/mage2pro/core/issues/398
 * 2.1) https://www.php.net/manual/filter.examples.validation.php
 * 2.2) https://stackoverflow.com/a/12026863
 * 3) "Port `df_is_email()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/628
 * @used-by df_customer()
 * @used-by df_subscriber()
 * @param mixed $v
 */
function df_is_email($v):bool {return !!filter_var($v, FILTER_VALIDATE_EMAIL);}

/**
 * We need `==` here, not `===`: https://php.net/manual/function.is-int.php#35820
 * @see \Df\Zf\Validate\StringT\IntT::isValid()
 * @used-by df_is_nat()
 * @used-by \Df\Core\Text\Regex::matchInt()
 * @param mixed $v
 */
function df_is_int($v):bool {return is_numeric($v) && ($v == (int)$v);}