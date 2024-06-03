<?php
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-03-28 If the function is called from a closure, then it will go up through the stask until it leaves all closures.
 * 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
 * 2024-06-03
 * 1) "Use the `callable` type": https://github.com/mage2pro/core/issues/404
 * 2) `callable` can be nullable: https://github.com/mage2pro/core/issues/174#user-content-callable
 * @used-by df_caller_entry_m()
 * @used-by df_caller_f()
 * @used-by df_caller_m()
 * @used-by df_caller_mf()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by \Df\Framework\Log\Dispatcher::handle()
 * @param T|int|null|array(array(string => string|int)) $p [optional]
 * @return array(string => string|int)
 */
function df_caller_entry($p = 0, ?callable $f = null, array $skip = []):array {
	/**
	 * 2018-04-24
	 * I do not understand why did I use `2 + $offset` here before.
	 * Maybe the @uses array_slice() was included in the backtrace in previous PHP versions (e.g. PHP 5.6)?
	 * array_slice() is not included in the backtrace in PHP 7.1.14 and in PHP 7.0.27
	 * (I have checked it in the both XDebug enabled and disabled cases).
	 * 2019-01-14
	 * It seems that we need `2 + $offset` because the stack contains:
	 * 1) the current function: df_caller_entry
	 * 2) the function who calls df_caller_entry: df_caller_f, df_caller_m, or \Df\Framework\Log\Dispatcher::handle
	 * 3) the function who calls df_caller_f, df_caller_m, or \Df\Framework\Log\Dispatcher::handle: it should be the result.
	 * So the offset is 2.
	 * The previous code failed the @see \Df\API\Facade::p() method in the inkifi.com store.
	 * 2023-07-26
	 * 1) "`df_caller_entry()` detects the current entry incorrectly": https://github.com/mage2pro/core/issues/260
	 * 2) We do not need `2 + $offset` anymore because the current implementation of @uses df_bt() already uses `1 + $p`.
	 * So I changed `df_bt(df_bt_inc($p, 2))` to `df_bt(df_bt_inc($p))`
	 * 2023-07-27
	 * We really need `2 + $offset`.
	 * 1) The first entry in df_bt() is the caller of df_bt(). It is `df_caller_entry` in our case.
	 * 2) The second entry in df_bt() is the caller of `df_caller_entry`. It should be skipped too.
	 */
	$bt = df_bt(df_bt_inc($p, 2)); /** @var array(int => array(string => mixed)) $bt */
	return df_eta(df_first(df_bt_filter_head($bt, $skip, $f)));
}

/**
 * 2023-08-05
 * 2024-01-11 "Port `df_caller_entry_m` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/173
 * @used-by df_caller_module()
 * @used-by df_log_l()
 * @param T|int $p
 */
function df_caller_entry_m($p = 0):array {return df_eta(df_caller_entry(df_bt_inc($p), function(array $e):bool {return
	# 2023-07-26
	# "«The required key «class» is absent» is `df_log()` is called from `*.phtml`":
	# https://github.com/mage2pro/core/issues/259
	($c = df_bt_entry_class($e))
		# 2023-08-05
		# 1) "«Module 'Monolog_Logger' is not correctly registered» in `lib/internal/Magento/Framework/Module/Dir.php:62`":
		# https://github.com/mage2pro/core/issues/318
		# 2) `Monolog_Logger` is not a Magento module, so I added `df_module_enabled()`.
		&& df_module_enabled($c) /** @var string|null $c */
		# 2024-02-11
		# 1) "`df_caller_entry_m()` should skip `Df\Framework\Log\Dispatcher`": https://github.com/mage2pro/core/issues/349
		# 2) "`df_caller_entry_m()` should skip `Df\Framework\Log\*` classes": https://github.com/mage2pro/core/issues/350
		&& !df_starts_with($c, 'Df\Framework\Log\\')
	# 2023-07-26
	# "If `df_log()` is called from a `*.phtml`,
	# then the `*.phtml`'s module should be used as the log source instead of `Magento_Framework`":
	# https://github.com/mage2pro/core/issues/268
	|| df_bt_entry_is_phtml($e)
;}));}