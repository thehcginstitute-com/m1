<?php
/**
 * 2023-08-05
 * 2024-01-11 "Port `df_bt_entry_class` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/182
 * @see \Df\Qa\Trace\Frame::class_()
 * @used-by df_caller_entry_m()
 * @used-by df_caller_module()
 */
function df_bt_entry_class(array $e):string {return dfa($e, 'class', '');}

/**
 * 2023-07-26 "Implement `df_bt_entry_file()`": https://github.com/mage2pro/core/issues/279
 * 2024-01-11 "Port `df_bt_entry_file` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/175
 * @see \Df\Qa\Trace\Frame::file()
 * @used-by df_bt()
 * @used-by df_log_l()
 * @used-by df_bt_entry_is_phtml()
 * @used-by df_caller_module()
 * @used-by \Df\Qa\Trace::__construct()
 */
function df_bt_entry_file(array $e):string {return
	/**
	 * 2023-01-28
	 * 1) The 'file' key can be absent in a stack frame, e.g.:
	 *	{
	 *		"function": "loadClass",
	 *		"class": "Composer\\Autoload\\ClassLoader",
	 *		"type": "->",
	 *		"args": ["Df\\Framework\\Plugin\\App\\Router\\ActionList\\Interceptor"]
	 *	},
	 *	{
	 *		"function": "spl_autoload_call",
	 *		"args": ["Df\\Framework\\Plugin\\App\\Router\\ActionList\\Interceptor"]
	 *	},
	 * 2) «Argument 1 passed to df_starts_with() must be of the type string, null given,
	 * called in vendor/mage2pro/core/Qa/Trace.php on line 28»: https://github.com/mage2pro/core/issues/186
	 * 3) @see \Df\Qa\Trace\Frame::file()
	 */
	dfa($e, 'file', '')
;}

/**
 * 2023-07-28
 * 2024-01-11 "Port `df_bt_entry_func` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/176
 * @see \Df\Qa\Trace\Frame::function_()
 * @used-by df_log_l()
 */
function df_bt_entry_func(array $e):string {return dfa($e, 'function', '');}