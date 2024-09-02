<?php
use Df\Qa\Failure\Exception as QE;
use Exception as E;
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2017-01-11
 * 2024-01-10 "Port the latest version of `df_log_l` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/169
 * @used-by df_log()
 * @used-by df_log_e()
 * @used-by dfp_report()
 * @used-by Df_Core_Model_Layout::_getBlockInstance()
 * @param string|object|null $m
 * @param string|mixed[]|E $p2
 * @param string|mixed[]|E $p3 [optional]
 * @param string|bool|null $p4 [optional]
 */
function df_log_l($m, $p2, $p3 = [], string $p4 = ''):void {
	/** @var T|null $t */ /** @var array|string|mixed $d */ /** @var string $suf */ /** @var string $pref */
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$t, $d, $suf, $pref] = df_is_th($p2) ? [$p2, $p3, $p4, ''] : [null, $p2, df_ets($p3), $p4];
	$m = $m ?: ($t ? df_caller_module($t) : df_caller_module());
	if (!$suf) {
		# 2023-07-26
		# 1) "If `df_log_l()` is called from a `*.phtml`,
		# then the `*.phtml`'s base name  should be used as the log file name suffix instead of `df_log_l`":
		# https://github.com/mage2pro/core/issues/269
		# 2) 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
		$entry = $t ? df_caller_entry_m($t) : df_caller_entry(0, null, ['df_log']); /** @var array(string => string|int) $entry */
		$suf = df_bt_entry_is_phtml($entry) ? basename(df_bt_entry_file($entry)) : df_bt_entry_func($entry);
	}
	$c = df_context(); /** @var array(string => mixed) $c */
	df_report(
		df_ccc('--', 'mage2.pro/' . df_ccc('-', df_report_prefix($m, $pref), '{date}--{time}'), $suf) .  '.log'
		# 2023-07-26
		# "`df_log_l()` should use the exception's trace instead of `df_bt_s(1)` for exceptions":
		# https://github.com/mage2pro/core/issues/261
		,df_cc_n(
			# 2023-07-28
			# "`df_log_l` does not log the context if the message is not an array":
			# https://github.com/mage2pro/core/issues/289
			/** @uses df_dump_ds() */
			df_map('df_dump_ds', !$d ? [$c] : (is_array($d) ? [dfa_merge_r($d, ['Mage2.PRO' => $c])] : [$d, $c]))
			,!$t ? '' : ['EXCEPTION', QE::i($t)->report(), "\n\n"]
			,($t ? null : "\n") . df_bt_s($t ?: 1)
		)
	);
}