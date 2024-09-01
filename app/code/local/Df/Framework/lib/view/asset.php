<?php
use Df\Core\Exception as DFE;
/**
 * 2015-12-29
 * $name could be:
 * 		1) a short name;
 * 		2) a full name composed with @see df_asset_name(). In this case, the function returns $name without changes.
 * @used-by df_block_output()
 * @param string|object|null $m [optional]
 */
function df_asset_name(string $name = '', $m = null, string $ext = ''):string {return df_ccc(
	'.', df_ccc('::', $m ? df_module_name($m) : null, $name ?: 'main'), $ext
);}