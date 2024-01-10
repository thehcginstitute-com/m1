<?php
use Exception as X;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2023-08-02
 * 2024-01-10 "Port `df_is_th` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/170
 * @see df_is_x()
 * @used-by df_bt()
 * @used-by df_bt_inc()
 * @used-by df_error_create()
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by df_xts()
 * @used-by df_xtsd()
 * @used-by \Df\Core\Exception::__construct()
 */
function df_is_th($v):bool {return $v instanceof T;}

/**
 * @used-by df_lx()
 * @used-by df_lxts()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_xml_parse()
 * @used-by \Df\Qa\Failure\Error::check()
 * @used-by \Df\Qa\Failure\Error::log()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @param X|string $e
 * @return string
 */
function df_xts($e) {return df_adjust_paths_in_message(!$e instanceof X ? $e : $e->getMessage());}