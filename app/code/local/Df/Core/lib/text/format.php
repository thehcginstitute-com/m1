<?php
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