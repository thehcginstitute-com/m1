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
 * 2020-06-28
 * @see df_strip_ext()
 * @used-by df_module_file_name()
 */
function df_file_ext_add(string $f, string $ext = ''):string {return !$ext ? $f : df_append($f, ".$ext");}

/**
 * 2018-07-06
 * @used-by df_report()
 * @param string $f
 * @param string $ext
 * @return string
 */
function df_file_ext_def($f, $ext) {return df_file_ext($f) ? $f : df_trim_right($f, '.') . ".$ext";}

/**
 * 2015-04-01
 * Раньше алгоритм был таким: return preg_replace('#\.[^.]*$#', '', $file)
 * Новый вроде должен работать быстрее?
 * http://stackoverflow.com/a/22537165
 * @used-by Df_Adminhtml_Catalog_Product_GalleryController::uploadActionDf()
 * @used-by
 * @param string $file
 * @return mixed
 */
function df_strip_ext($file) {return pathinfo($file, PATHINFO_FILENAME);}