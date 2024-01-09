<?php
/**
 * 2015-11-28 http://stackoverflow.com/a/10368236
 * @used-by df_asset_create()
 * @used-by df_file_ext_def()
 * @used-by df_img_is_jpeg()
 * @param string $f
 * @return string
 */
function df_file_ext($f) {return pathinfo($f, PATHINFO_EXTENSION);}

/**
 * 2018-07-06
 * @used-by df_report()
 * @param string $f
 * @param string $ext
 * @return string
 */
function df_file_ext_def($f, $ext) {return df_file_ext($f) ? $f : df_trim_right($f, '.') . ".$ext";}