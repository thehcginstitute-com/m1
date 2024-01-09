<?php
use Exception as E;
/**
 * 2021-10-04
 * @used-by df_bt_has()
 * @used-by df_bt_s()
 * @used-by df_caller_entry()
 * @used-by dfs_con()
 * @param int $limit
 * @param E|int|null|array(array(string => string|int)) $p [optional]
 * @return array(array(string => mixed))
 */
function df_bt($p = 0, $limit = 0) {return is_array($p) ? $p : ($p instanceof E ? $p->getTrace() : df_slice(
	debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, !$limit ? 0 : 1 + $p + $limit), 1 + $p, $limit
));}

/**
 * 2021-10-04
 * @used-by df_bt_log()
 * @used-by df_bt_s()
 * @used-by df_caller_entry()
 * @param E|int|null|array(array(string => string|int)) $p
 * @param int $o
 * @return E|int
 */
function df_bt_inc($p, $o = 1) {return is_array($p) || $p instanceof E ? $p : $o + $p;}

/**
 * 2019-12-16
 * $p позволяет при записи стека вызовов пропустить несколько последних вызовов функций,
 * которые и так очевидны (например, вызов данной функции, вызов df_bt_log() и т.п.)
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @param int|E|array(array(string => string|int)) $p
 * @return string
 */
function df_bt_s($p = 0) {return Formatter::p(new Trace(df_bt(df_bt_inc($p))));}