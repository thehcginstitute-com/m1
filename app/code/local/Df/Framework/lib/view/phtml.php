<?php
/**
 * 2023-08-01
 * 2024-01-11 "Port `df_is_phtml` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/185
 * @used-by df_bt_entry_is_phtml()
 * @used-by \Df\Qa\Trace\Frame::isPHTML()
 */
function df_is_phtml(string $f):bool {return df_ends_with($f, '.phtml');}