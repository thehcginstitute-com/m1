<?php
use Df\Qa\Method as Q;
use Exception as E;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2020-01-21
 * @param mixed $cond
 * @param null $m
 * @return mixed
 * @throws E
 */
function df_assert($cond, $m = null) {return $cond ?: df_error($m);}

/**
 * @used-by df_currency_base()
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @param string $v
 * @param int $sl [optional]
 * @return string
 * @throws E
 */
function df_assert_sne($v, $sl = 0) {
	$sl++;
	# The previous code `if (!$v)` was wrong because it rejected the '0' string.
	return !df_es($v) ? $v : Q::raiseErrorVariable(__FUNCTION__, [Q::NES], $sl);
}

/**
 * 2016-08-09
 * 2024-03-05 "Port `df_assert_traversable()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/459
 * @used-by dfaf()
 * @param Traversable|array $v
 * @param string|Th $m [optional]
 * @return Traversable|array
 * @throws E
 */
function df_assert_traversable($v, $m = null) {return is_iterable($v) ? $v : df_error($m ?:
	'A variable is expected to be a Traversable or an array, ' . 'but actually it is %s.', df_type($v)
);}

/**
 * @used-by \HetNieuweWeb_CustomerNavigation_Block_Customer_Account_Navigation::removeLinkByName()
 * @param mixed $v
 */
function df_bool($v):bool {
	/**
	 * Unfortunately, we can not replace @uses in_array() with @see array_flip() + @see isset() to speedup the execution,
	 * because it could lead to the warning: «Warning: array_flip(): Can only flip STRING and INTEGER values!».
	 * Moreover, @see array_flip() + @see isset() fails the following test:
	 *	$a = array(null => 3, 0 => 4, false => 5);
	 *	$this->assertNotEquals($a[0], $a[false]);
	 * Though, @see array_flip() + @see isset() does not fail the tests:
	 * $this->assertNotEquals($a[null], $a[0]);
	 * $this->assertNotEquals($a[null], $a[false]);
	 */
	static $no = [0, '0', 'false', false, null, 'нет', 'no', 'off', '']; /** @var mixed[] $no */
	static $yes = [1, '1', 'true', true, 'да', 'yes', 'on']; /** @var mixed[] $yes */
	/**
	 * Passing $strict = true to the @uses in_array() call is required here,
	 * otherwise any true-compatible value (e.g., a non-empty string) will pass the check.
	 */
	return in_array($v, $no, true) ? false : (in_array($v, $yes, true) ? true : df_error(
		'A boolean value is expected, but got «%s».', df_dump($v)
	));
}