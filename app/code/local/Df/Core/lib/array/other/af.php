<?php
/**
 * 2017-02-18 [array|callable, array|callable] => [array, callable]
 * 2024-03-05 "Port `dfaf()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/461
 * @used-by df_filter_f()
 * @used-by df_find()
 * @used-by df_map()
 * @used-by dfak_transform()
 * @param array|callable|Traversable $a
 * @param array|callable|Traversable $b
 * @return array(int|string => mixed)
 */
function dfaf($a, $b):array {
	# 2020-02-15
	# "A variable is expected to be a traversable or an array, but actually it is a «object»":
	# https://github.com/tradefurniturecompany/site/issues/36
	$ca = is_callable($a); /** @var bool $ca */
	$cb = is_callable($b); /** @var bool $ca */
	if (!$ca || !$cb) {
		df_assert($ca || $cb);
		$r = $ca ? [df_assert_traversable($b), $a] : [df_assert_traversable($a), $b];
	}
	else {
		$ta = is_iterable($a); /** @var bool $ta */
		$tb = is_iterable($b); /** @var bool $tb */
		if ($ta && $tb) {
			df_error('dfaf(): both arguments are callable and traversable: %s and %s.', df_type($a), df_type($b));
		}
		df_assert($ta || $tb);
		$r = $ta ? [$a, $b] : [$b, $a];
	}
	return $r;
}