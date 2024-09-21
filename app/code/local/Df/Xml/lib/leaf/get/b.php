<?php
use SimpleXMLElement as CX;

/**
 * @used-by df_module_enabled()
 * @param bool|callable $d [optional]
 */
function df_leaf_b(?CX $e = null, $d = false):bool {return df_bool(df_leaf($e, $d));}