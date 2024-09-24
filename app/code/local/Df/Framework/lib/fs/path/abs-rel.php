<?php
/**
 * 2023-07-25 "`df_path_absolute()` is wrongly implemented": https://github.com/mage2pro/core/issues/270
 * @see df_sys_path_abs()
 * @used-by df_contents()
 */
function df_path_abs(string $p):string {
	$bp = df_path_n(BP);
	$p = df_path_n($p);
	/** 2023-07-26 Similar to @see df_prepend() */
	return df_starts_with($p, $bp) ? $p : df_cc_path($bp, df_trim_ds_left($p));
}

/**
 * 2015-12-06 It trims the ending «/».
 * 2024-06-09 "`df_path_rel()` → `df_path_rel()`": https://github.com/mage2pro/core/issues/407
 * @used-by df_file_write()
 * @used-by df_media_path_rel()
 * @used-by df_module_name_by_path()
 * @used-by df_product_images_path_rel()
 * @used-by Df\Qa\Failure\Error::preface()
 * @used-by Df\Qa\Trace\Frame::file()
 */
function df_path_rel(string $p):string {return df_trim_text_left(
	df_trim_ds_left(df_path_n($p)), df_trim_ds_left(df_path_n(BP . '/'))
);}

/**
 * 2016-12-23
 * Удаляет из сообщений типа
 * «Warning: Division by zero in C:\work\mage2.pro\store\vendor\mage2pro\stripe\Method.php on line 207»
 * файловый путь до папки Magento.
 * 2024-06-09 "`df_path_rel_g` → `df_path_rel_g`": https://github.com/mage2pro/core/issues/410
 * @used-by df_xts()
 * @used-by df_xtsd()
 * @used-by Df\Qa\Failure\Error::msg()
 */
function df_path_rel_g(string $m):string {
	$bpLen = mb_strlen(BP); /** @var int $bpLen */
	do {
		$begin = mb_strpos($m, BP); /** @var int|false $begin */
		if (false === $begin) {
			break;
		}
		$end = mb_strpos($m, '.php', $begin + $bpLen); /** @var int|false $end */
		if (false === $end) {
			break;
		}
		$end += 4; # 2016-12-23 It is the length of the «.php» suffix.
		$m =
			mb_substr($m, 0, $begin)
			# 2016-12-23 I use `+ 1` to cut off a slash («/» or «\») after BP.
			. df_path_n(mb_substr($m, $begin + $bpLen + 1, $end - $begin - $bpLen - 1))
			. mb_substr($m, $end)
		;
	} while(true);
	return $m;
}