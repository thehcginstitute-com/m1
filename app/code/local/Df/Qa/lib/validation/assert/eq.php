<?php
use Exception as E;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2024-01-11 "Port `df_assert_eq` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/186
 * @used-by df_module_name_by_path()
 * @used-by df_vector_sum()
 * @used-by \Df\Qa\Trace\Frame::url()
 * @param string|int|float|bool $expected
 * @param string|int|float|bool $v
 * @param string|Th|null $m [optional]
 * @return string|int|float|bool
 * @throws E
 */
function df_assert_eq($expected, $v, $m = null) {return $expected === $v ? $v : df_error($m ?: sprintf(
	"Expected «%s», got «%s».", df_dump($expected), df_dump($v)
));}

/**
 * 2024-01-10 "Port `df_assert_lt` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/164
 * @used-by \Df\Qa\Trace\Frame::methodParameter()
 * @param int|float $highBound
 * @param int|float $v
 * @param string|Th|null $m [optional]
 * @return int|float
 * @throws E
 */
function df_assert_lt($highBound, $v, $m = null) {return $highBound >= $v ? $v : df_error($m ?:
	"A number < $highBound is expected, but got $v."
);}

/**
 * @used-by df_action_name()
 * @used-by df_contents()
 * @used-by df_file_name()
 * @used-by df_json_decode()
 * @param string|int|float|bool $neResult
 * @param string|int|float|bool $v
 * @param string|Th|null $m [optional]
 * @return string|int|float|bool
 * @throws E
 */
function df_assert_ne($neResult, $v, $m = null) {return $neResult !== $v ? $v : df_error($m ?:
	"The value $v is rejected, any other is allowed."
);}