<?php
/**
 * 2015-12-01 Отныне всегда используем `/` вместо @see DIRECTORY_SEPARATOR
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * 2024-06-09 "`df_cc_path()` should trim internal `/` and `DS` for arguments": https://github.com/mage2pro/core/issues/406
 * @used-by df_config_e()
 * @used-by df_db_credentials()
 * @used-by df_fs_etc()
 * @used-by df_img_resize()
 * @used-by df_js()
 * @used-by df_js_x()
 * @used-by df_module_name_by_path()
 * @used-by df_path_abs()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @param string|string[] ...$a
 */
function df_cc_path(...$a):string {
	$a = df_clean(dfa_flatten($a));
	$s = df_path_n(implode($a)); /** @var string $s */
	return
		(df_starts_with($s, '/') ? '/' : '')
		. implode('/', df_trim_ds($a))
		. (df_ends_with($s, '/') && 1 < mb_strlen($s) ? '/' : '')
	;
}

/**
 * 2016-05-31
 * 2022-11-26 We can not declare the argument as `string ...$a` because such a syntax rejects arrays: https://3v4l.org/jFdPm
 * 2023-01-01 @deprecated It is unused.
 * @param string|string[] ...$a
 */
function df_cc_path_t(...$a):string {return df_append(df_cc_path(dfa_flatten($a)), '/');}