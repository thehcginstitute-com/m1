<?php
use Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2024-02-03 "Port `df_format()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/346
 * @used-by df_error_create()
 * @used-by \Df\Core\Exception::comment()
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
 * 2017-07-09
 * @used-by \Df\Qa\Failure\Error::preface()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @param array(string => string) $a
 * @param int $pad
 * @return string
 */
function df_kv(array $a, $pad = 0) {return df_cc_n(df_map_k(df_clean($a), function($k, $v) use($pad) {return
	(!$pad ? "$k: " : df_pad("$k:", $pad))
	.(is_array($v) || (is_object($v) && !method_exists($v, '__toString')) ? "\n" . df_json_encode($v) : $v)
;}));}

/**
 * 2024-02-03 "Port `df_sprintf()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/349
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
	list($s, $args) = is_array($s) ? [df_first($s), $s] : [$s, func_get_args()];
	try {$r = df_sprintf_strict($args);}
	# 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	catch (Th $th) {$r = $s;}
	return $r;
}

/**
 * 2016-03-09 Замещает переменные в тексте.
 * 2016-08-07 Сегодня разработал аналогичные функции для JavaScript: df.string.template() и df.t().
 * @used-by df_file_name()
 * @param string $s
 * @param array(string => string) $variables
 * @param string|callable|null $onUnknown
 * @return string
 */
function df_var($s, array $variables, $onUnknown = null) {return preg_replace_callback(
	'#\{([^\}]*)\}#ui', function($m) use($variables, $onUnknown) {return
		dfa($variables, dfa($m, 1, ''), $onUnknown)
	;}, $s
);}