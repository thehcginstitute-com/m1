<?php
/**
 * 2016-12-23
 * Удаляет из сообщений типа
 * «Warning: Division by zero in C:\work\mage2.pro\store\vendor\mage2pro\stripe\Method.php on line 207»
 * файловый путь до папки Magento.
 * @used-by df_xts()
 * @used-by df_xtsd()
 * @used-by \Df\Qa\Failure\Error::msg()
 * @param string $m
 * @return string
 */
function df_adjust_paths_in_message($m) {
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

/**
 * Заменяет все сиволы пути на /
 * 2021-12-17 https://3v4l.org/8iP17
 * @see df_path_n_real()
 * @used-by df_adjust_paths_in_message()
 * @used-by df_bt_s()
 * @used-by df_class_file()
 * @used-by df_explode_path()
 * @used-by df_file_name()
 * @used-by df_path_is_internal()
 * @used-by df_path_relative()
 * @used-by df_product_image_url()
 * @param string $p
 * @return string
 */
function df_path_n($p) {return str_replace(['\/', '\\'], '/', $p);}

/**
 * 2015-12-06 It trims the ending «/».
 * @param string $p
 * @param string $b [optional]
 * @return string
 */
function df_path_relative($p, $b = BP) {return df_trim_text_left(
	df_trim_ds_left(df_path_n($p)), df_trim_ds_left(df_path_n($b))
);}