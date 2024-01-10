<?php
use Df\Core\Json as J;

/**
 * 2023-08-04 "Implement `df_json_dont_sort()`": https://github.com/mage2pro/core/issues/313
 * 2024-01-11 "Port `df_json_dont_sort` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/187
 * @see df_json_sort()
 * @used-by df_dump_ds()
 * @return mixed
 */
function df_json_dont_sort(F $f) {/** @var mixed $r */
	$prev = J::bSort(); /** @var bool $prev */
	J::bSort(false);
	try {$r = $f();}
	finally {J::bSort($prev);}
	return $r;
}

/**
 * 2015-12-06
 * @used-by df_json_encode_partial()
 * @param mixed $v
 * @param int $flags
 * @return string
 */
function df_json_encode($v, $flags = 0) {return json_encode(df_json_sort($v),
	JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE | $flags
);}

/**
 * 2020-02-15
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param mixed $v
 * @return string
 */
function df_json_encode_partial($v) {return df_json_encode($v, JSON_PARTIAL_OUTPUT_ON_ERROR);}

/**
 * 2017-09-07
 * I use the @uses df_is_assoc() check,
 * because otherwise @uses df_ksort_r_ci() will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @used-by df_json_decode()
 * @used-by df_json_encode()
 * @param mixed $v
 * @return mixed
 */
function df_json_sort($v) {return !is_array($v) ? $v : (df_is_assoc($v) ? df_ksort_r_ci($v) : $v);}