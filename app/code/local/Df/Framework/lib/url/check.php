<?php
/**
 * http://stackoverflow.com/a/15011528
 * http://www.php.net/manual/en/function.filter-var.php
 * filter_var('/C/A/CA559AWLE574_1.jpg', FILTER_VALIDATE_URL) returns `false`.
 * 2023-07-26
 * 1) "`df_check_url` → `df_is_url`": https://github.com/mage2pro/core/issues/276
 * 2) df_is_url('php://input') returns `true`:
 * https://github.com/mage2pro/core/issues/277
 * https://3v4l.org/mTt87
 * @used-by df_contents()
 * @used-by df_url_bp()
 */
function df_is_url(string $s):bool {return false !== filter_var($s, FILTER_VALIDATE_URL);}