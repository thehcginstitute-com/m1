<?php
/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L109-L111
 * 2017-01-12
 * The df_caller_ff() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L113-L123
 * 2020-07-08 The function's new implementation is from the previous df_caller_ff() function.
 * @used-by df_log_e()
 * @used-by df_log_l()
 * @used-by df_oqi_amount()
 * @used-by df_prop()
 * @param int $o [optional]
 * @return string
 */
function df_caller_f($o = 0) {return df_caller_entry(++$o)['function'];}