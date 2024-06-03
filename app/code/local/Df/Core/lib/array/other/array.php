<?php
/**
 * @used-by df_con_hier_suf_ta()
 * @used-by df_explode_xpath()
 * @used-by df_fe_init()
 * @used-by df_find()
 * @used-by df_map()
 * @param mixed|mixed[] $v
 */
function df_array($v):array {return is_array($v) ? $v : [$v];}