<?php
use Exception as E;

/**
 * @used-by df_lx()
 * @used-by df_lxts()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_xml_parse()
 * @used-by \Df\Qa\Failure\Error::check()
 * @used-by \Df\Qa\Failure\Error::log()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @param E|string $e
 * @return string
 */
function df_xts($e) {return df_adjust_paths_in_message(!$e instanceof E ? $e : $e->getMessage());}