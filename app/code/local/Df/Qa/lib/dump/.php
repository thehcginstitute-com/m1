<?php
use Df\Qa\Dumper;
/**
 * We do not use @uses \Df\Qa\Dumper as a singleton
 * because @see \Df\Qa\Dumper::dumpObject()
 * uses the @see \Df\Qa\Dumper::$_dumped property to avoid a recursion.
 * @see df_kv()
 * @see df_string()
 * @see df_type()
 * @used-by df_assert_eq()
 * @used-by df_bool()
 * @used-by df_caller_m()
 * @used-by df_dump_ds()
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_sentry()
 * @used-by df_sprintf_strict()
 * @used-by df_string()
 * @used-by df_type()
 * @used-by dfa_assert_keys()
 * @used-by dfc()
 * @used-by dfs_con()
 * @used-by Df\Zf\Validate::message()
 * @param mixed $v
 */
function df_dump($v):string {return Dumper::i()->dump($v);}

/**
 * 2023-08-04
 * @used-by df_log_l()
 * @used-by Df\Qa\Failure\Exception::postface()
 */
function df_dump_ds($v):string {return df_json_dont_sort(function() use($v):string {return df_dump($v);});}