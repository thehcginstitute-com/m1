<?php
/**
 * 2015-11-30
 * @used-by df_fs_etc()
 * @used-by df_path_absolute()
 * @used-by df_path_relative()
 * @used-by df_product_image_path2abs()
 * @used-by df_replace_store_code_in_url()
 * @param string $p
 * @return string
 */
function df_trim_ds_left($p) {return df_trim_left($p, '/\\');}