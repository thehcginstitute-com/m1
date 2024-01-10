<?php
/**
 * 2023-08-05
 * 2024-01-11 "Port `df_bt_entry_class` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/182
 * @see \Df\Qa\Trace\Frame::class_()
 * @used-by df_caller_entry_m()
 * @used-by df_caller_module()
 */
function df_bt_entry_class(array $e):string {return dfa($e, 'class', '');}