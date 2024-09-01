<?php
/**
 * Заменяет все сиволы пути на /
 * 2021-12-17 https://3v4l.org/8iP17
 * @see df_path_n_real()
 * @used-by df_path_rel_g()
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
 * @used-by df_module_name_by_path()
 * @used-by \Df\Qa\Failure\Error::preface()
 * @used-by \Df\Qa\Trace\Frame::file()
 */
function df_path_relative(string $p):string {return df_trim_text_left(
	df_trim_ds_left(df_path_n($p)), df_trim_ds_left(df_path_n(BP . '/'))
);}