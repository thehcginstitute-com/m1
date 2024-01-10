<?php
/**
 * 2023-07-26
 * "If `df_log()` is called from a `*.phtml`,
 * then the `*.phtml`'s module should be used as the log source instead of `Magento_Framework`":
 * https://github.com/mage2pro/core/issues/268
 * 2024-01-11 "Port `df_module_name_by_path` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/183
 * @used-by df_caller_module()
 */
function df_module_name_by_path(string $f):string {/** @var string $r */
	$f = df_path_relative($f);
	$f2 = df_trim_ds_left(df_trim_text_left(df_trim_text_left($f, 'app/code/'), ['community', 'core', 'local']));
	$err = "Unable to detect the module for the file: `$f`"; /** @var string $err */
	df_assert_ne($f, $f2, $err);
	$a = array_slice(df_explode_xpath($f2), 0, 2); /** @var string[] $a */
	df_assert_eq(2, count($a), $err);
	return implode('_', $a);
}