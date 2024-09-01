<?php
/**
 * Заменяет все сиволы пути на /
 * 2021-12-17 https://3v4l.org/8iP17
 * @see df_path_n_real()
 * @used-by df_bt_s()
 * @used-by df_cc_path()
 * @used-by df_class_file()
 * @used-by df_explode_path()
 * @used-by df_file_name()
 * @used-by df_path_abs()
 * @used-by df_path_is_internal()
 * @used-by df_path_rel()
 * @used-by df_path_rel_g()
 * @used-by df_product_image_url()
 */
function df_path_n(string $p):string {return str_replace(['\/', '\\'], '/', $p);}

/**
 * 2016-12-30 It replaces all path delimiters with @uses DS
 * 2021-12-17 https://3v4l.org/OGUh6
 * @see df_path_n()
 */
function df_path_n_real(string $p):string {return str_replace(['\/', '\\', '/'], DS, $p);}
