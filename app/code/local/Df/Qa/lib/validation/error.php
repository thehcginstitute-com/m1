<?php
use Exception as E;

/**
 * 2020-01-21
 * 2024-03-16 "Port `df_error` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/487
 * @used-by df_assert()
 * @param string|string[]|mixed|E|null ...$m
 * @throws E
 */
function df_error(...$m) {throw $m instanceof E ? $m : new E(
	is_null($m) ? null : (is_array($m) ? implode("\n\n", $m) : sprintf(...$m))
);}