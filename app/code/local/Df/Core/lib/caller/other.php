<?php
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-11-19
 * @used-by df_abstract()
 * @used-by df_sentry_ext_f()
 */
function df_caller_c(int $o = 0):string {return df_first(df_explode_method(df_caller_m(++$o)));}

/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L109-L111
 * 2017-01-12
 * The df_caller_ff() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L113-L123
 * 2020-07-08 The function's new implementation is from the previous df_caller_ff() function.
 * @used-by df_oqi_amount()
 * @used-by df_prop()
 */
function df_caller_f(int $o = 0):string {return df_caller_entry(++$o)['function'];}

/**
 * 2016-08-10
 * The original (not used now) implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L125-L136
 * 2017-03-28
 * The df_caller_mm() implementation: https://github.com/mage2pro/core/blob/6.7.3/Core/lib/caller.php#L155-L169
 * 2020-07-08 The function's new implementation is from the previous df_caller_mm() function.
 * 2023-07-26
 * 1) «Array to string conversion in vendor/mage2pro/core/Core/lib/caller.php on line 114»
 * https://github.com/mage2pro/core/issues/257
 * 2) The pevious error handling never worked correctly:
 * https://github.com/mage2pro/core/tree/9.8.4/Core/lib/caller.php#L114
 * @used-by df_cache_get_simple()
 * @used-by df_caller_c()
 * @used-by df_caller_ml()
 * @used-by df_prop()
 * @used-by df_sentry_extra_f()
 */
function df_caller_m(int $o = 0):string {return df_cc_method(df_assert(df_caller_entry(++$o,
	# 2023-07-26
	# "«The required key «class» is absent» is `df_log()` is called from `*.phtml`":
	# https://github.com/mage2pro/core/issues/259
	'df_bt_entry_is_method' /** @uses df_bt_entry_is_method() */
)));}

/**
 * 2023-07-25
 * 2023-07-26
 * The previous implementation:
 * 		return df_module_name(df_caller_c(++$o))
 * https://github.com/mage2pro/core/blob/9.9.5/Core/lib/caller.php#L147
 * 2024-01-10 "Port `df_caller_module` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/172
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by df_sentry_m()
 * @param T|int $p
 */
function df_caller_module($p = 0):string {return !($e = df_caller_entry_m(df_bt_inc($p))) ? 'Df_Core' : (
	# 2023-08-05 «Module 'Monolog_Logger::addRecord' is not correctly registered»: https://github.com/mage2pro/core/issues/317
	df_bt_entry_is_method($e) ? df_module_name(df_bt_entry_class($e)) : df_module_name_by_path(df_bt_entry_file($e))
);}

/**
 * 2024-03-03
 * @used-by df_no_rec()
 */
function df_caller_mf(int $o = 0):string {return df_cc_method(df_assert(df_caller_entry(++$o)));}