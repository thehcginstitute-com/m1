<?php
use Df\Qa\Trace;
use Df\Qa\Trace\Formatter;
use Throwable as Th; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2019-12-16
 * $p позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt_log() и т.п.)
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @param int|Th|array(array(string => string|int)) $p
 */
function df_bt_s($p = 0):string {return Formatter::p(new Trace(df_bt(df_bt_inc($p))));}