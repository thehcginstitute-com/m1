<?php
use Exception as E;
use Varien_Object as _DO;
/**
 * @param _DO|mixed[]|mixed|E $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null) {df_log_l($m, $v);}

/**
 * 2017-01-11
 * @used-by df_log()
 * @used-by df_log_e()
 * @used-by dfp_report()
 * @param string|object|null $m
 * @param string|mixed[]|E $p2
 * @param string|mixed[]|E $p3 [optional]
 * @param string|bool|null $p4 [optional]
 */
function df_log_l($m, $p2, $p3 = [], $p4 = null) {
	/** @var E|null $e */ /** @var array|string|mixed $d */ /** @var string|null $suf */ /** @var string|null $pref */
	list($e, $d, $suf, $pref) = $p2 instanceof E ? [$p2, $p3, $p4, null] : [null, $p2, $p3, $p4];
	if (!$m && $e) {
		/** @var array(string => string) $en */
		$en = df_caller_entry($e, function(array $e) {return ($c = dfa($e, 'class')) && df_module_enabled($c);});
		list($m, $suf) = [dfa($en, 'class'), dfa($en, 'function', 'exception')];
	}
	$suf = $suf ?: df_caller_f();
	if (is_array($d)) {
		$d = df_extend($d, ['Mage2.PRO' => df_context()]);
	}
	$d = !$d ? null : (is_string($d) ? $d : df_json_encode($d));
	df_report(
		df_ccc('--', 'mage2.pro/' . df_ccc('-', df_report_prefix($m, $pref), '{date}--{time}'), $suf) .  '.log'
		,df_cc_n($d, !$e ? null : ['EXCEPTION', QE::i($e)->report(), "\n\n"], df_bt_s(1))
	);
}

/**
 * 2017-04-03
 * 2017-04-22
 * С не-строковым значением $m @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $m will be written on a new line.
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @used-by \Df\Qa\Failure\Error::log()
 * @param string $f
 * @param string $m
 * @param bool $append [optional]
 */
function df_report($f, $m, $append = false) {
	if (!df_es($m)) {
		$f = df_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
	}
}