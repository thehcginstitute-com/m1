<?php
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**  
 * 2020-02-04
 * @used-by dfp_card_format_last4()
 * @used-by dfp_methods()
 */
function df_desc(string $s1, string $s2):string {return df_es($s1) ? $s2 : (df_es($s2) || $s2 === $s1 ? $s1 : "$s1 ($s2)");}

/**
 * @used-by df_checkout_error()
 * @used-by df_error_create()
 * @used-by \Df\Payment\W\Exception::__construct()
 * @param mixed ...$a
 */
function df_format(...$a):string { /** @var string $r */
	$a = df_args($a);
	$r = null;
	switch (count($a)) {
		case 0:
			$r = '';
			break;
		case 1:
			$r = $a[0];
			break;
		case 2:
			if (is_array($a[1])) {
				$r = strtr($a[0], $a[1]);
			}
			break;
	}
	return !is_null($r) ? $r : df_sprintf($a);
}

/**
 * @used-by df_format()
 * @param string|mixed[] $s
 * @throws Th
 */
function df_sprintf($s):string {/** @var string $r */ /** @var mixed[] $args */
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$s, $args] = is_array($s) ? [df_first($s), $s] : [$s, func_get_args()];
	try {$r = df_sprintf_strict($args);}
	# 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	catch (Th $th) {$r = $s;}
	return $r;
}

/**
 * @used-by df_sprintf()
 * @param string|mixed[] $s
 * @throws Th
 */
function df_sprintf_strict($s):string {/** @var string $r */ /** @var mixed[] $args */
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	list($s, $args) = is_array($s) ? [df_first($s), $s] : [$s, func_get_args()];
	if (1 === count($args)) {
		$r = $s;
	}
	else {
		try {$r = vsprintf($s, df_tail($args));}
		# 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
		catch (Th $th) {
			/**
			 * 2024-03-03
			 * The previous (wrong) code:
			 * 		static $inProcess = false;
			 * 		if (!$inProcess) {
			 * https://github.com/mage2pro/core/blob/10.6.0/Core/lib/text/format.php#L118-L119
			 * https://3v4l.org/a6oIr
			 */
			df_no_rec(function() use($s, $th, $args):void {
				df_error(
					'df_sprintf_strict failed: «{message}».'
					. "\nPattern: {$s}."
					. "\nParameters:\n{params}."
					,['{message}' => df_xts($th), '{params}' => df_dump(df_tail($args))]
				);
			});
		}
	}
	return $r;
}

/**
 * 2016-03-09 Замещает переменные в тексте.
 * 2016-08-07 Сегодня разработал аналогичные функции для JavaScript: df.string.template() и df.t().
 * @used-by df_file_name()
 * @used-by \Dfe\GingerPaymentsBase\Block\Info::btInstructions()
 * @used-by \Df\Payment\Charge::text()
 * @used-by \Df\Payment\Settings::messageFailure()
 * @used-by \Dfe\SalesSequence\Plugin\Model\Manager::affix()
 * @param array(string => string) $variables
 * @param string|callable|null $onUnknown
 */
function df_var(string $s, array $variables, $onUnknown = null):string {return preg_replace_callback(
	'#\{([^\}]*)\}#ui', function($m) use($variables, $onUnknown) {return
		dfa($variables, dfa($m, 1, ''), $onUnknown)
	;}, $s
);}