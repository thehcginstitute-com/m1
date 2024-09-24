<?php
use Closure as F;
use Df\Core\Exception as DFE;
use Df\Core\Json as J;
/**
 * 2016-07-18
 * Видел решение здесь: http://stackoverflow.com/a/6041773
 * Но оно меня не устроило. И без собаки будет Warning.
 * @used-by df_check_json_complex()
 * @used-by Df\Qa\Dumper::dumpS()
 * @param mixed $v
 */
function df_check_json($v):bool {return !is_null(@json_decode($v));}

/**
 * 2016-08-19
 * @see json_decode() спокойно принимает не только строки, но и числа, а также true.
 * Наша функция возвращает true, если аргумент является именно строкой.
 * @param mixed $v
 */
function df_check_json_complex($v):bool {return is_string($v) && df_starts_with($v, '{') && df_check_json($v);}

/**
 * «Returns the value encoded in json in appropriate PHP type.
 * Values true, false and null are returned as TRUE, FALSE and NULL respectively.
 * NULL is returned if the json cannot be decoded or if the encoded data is deeper than the recursion limit.»
 * https://php.net/manual/function.json-decode.php
 * @used-by df_cache_get_simple()
 * @used-by df_ci_get()
 * @used-by df_github_request()
 * @used-by df_http_json()
 * @used-by df_json_file_read()
 * @used-by df_json_prettify()
 * @used-by df_module_json()
 * @used-by df_oi_get()
 * @used-by df_oro_get_list()
 * @used-by df_package()
 * @used-by df_request_body_json()
 * @used-by df_test_file_lj()
 * @used-by app/design/frontend/MageSuper/magestylish/Cart2Quote_Quotation/templates/email/proposal/items/quote/bundle.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/68)
 * @used-by app/design/frontend/MageSuper/magestylish/Cart2Quote_Quotation/templates/email/quote/items/quote/bundle.phtml (canadasatellite.ca, https://github.com/canadasatellite-ca/site/issues/67)
 * @param string|null $s
 * @return array|mixed|bool|null
 * @throws DFE
 */
function df_json_decode($s, bool $throw = true) {/** @var mixed|bool|null $r */
	# 2015-12-19 У PHP 7.0.1 декодировании пустой строки почему-то приводит к сбою: «Decoding failed: Syntax error».
	# 2022-10-14
	# «an empty string is no longer considered valid JSON»:
	# https://php.net/manual/migration70.incompatible.php#migration70.incompatible.other.json-to-jsond
	# 2022-11-24 `json_decode(false)` and `json_decode('')` return `NULL`: https://3v4l.org/PCdlG
	if (df_nes($s)) {
		$r = $s;
	}
	else {
		# 2016-10-30
		# json_decode('7700000000000000000000000') возвращает 7.7E+24
		# https://3v4l.org/NnUhk
		# http://stackoverflow.com/questions/28109419
		# Такие длинные числоподобные строки используются как идентификаторы КЛАДР
		# (модулем доставки «Деловые Линии»), и поэтому их нельзя так корёжить.
		# Поэтому используем константу JSON_BIGINT_AS_STRING
		# https://3v4l.org/vvFaF
		$r = json_decode($s, true, 512, JSON_BIGINT_AS_STRING);
		# 2016-10-28
		# json_encode(null) возвращает строку 'null', а json_decode('null') возвращает null.
		# Добавил проверку для этой ситуации, чтобы не считать её сбоем.
		if (is_null($r) && 'null' !== $s && $throw) {
			df_assert_ne(JSON_ERROR_NONE, json_last_error());
			df_error(
				"Parsing a JSON document failed with the message «%s».\nThe document:\n{$s}"
				,json_last_error_msg()
			);
		}
	}
	return df_json_sort($r);
}

/**
 * 2023-08-04 "Implement `df_json_dont_sort()`": https://github.com/mage2pro/core/issues/313
 * @see df_json_sort()
 * @used-by df_dump_ds()
 * @return mixed
 */
function df_json_dont_sort(F $f) {/** @var mixed $r */
	$prev = J::bSort(); /** @var bool $prev */
	J::bSort(false);
	try {$r = $f();}
	finally {J::bSort($prev);}
	return $r;
}

/**
 * 2015-12-06
 * @used-by df_ci_add()
 * @used-by df_ejs()
 * @used-by df_js_x()
 * @used-by df_json_encode_partial()
 * @used-by df_json_prettify()
 * @used-by df_oi_add()
 * @used-by df_widget()
 * @used-by dfp_container_add()
 * @used-by Df\Core\O::j()
 * @used-by Df\Qa\Dumper::dumpArray()
 * @used-by vendor/wolfautoparts.com/filter/view/frontend/templates/sidebar.phtml
 * @param mixed $v
 */
function df_json_encode($v, int $flags = 0):string {return json_encode(df_json_sort($v),
	JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE | $flags
);}

/**
 * 2020-02-15
 * @used-by Df\Qa\Dumper::dumpObject()
 * @param mixed $v
 */
function df_json_encode_partial($v):string {return df_json_encode($v, JSON_PARTIAL_OUTPUT_ON_ERROR);}

/**
 * 2022-10-15
 * 2023-07-26 `df_json_file_read` should accept internal paths: https://github.com/mage2pro/core/issues/278
 * @used-by df_credentials()
 * @used-by df_module_json()
 * @return array|bool|mixed|null
 * @throws DFE
 */
function df_json_file_read(string $p) {return df_json_decode(df_contents($p));}

/**
 * 2017-07-05
 * @used-by Df\Qa\Dumper::dumpS()
 * @param string|array(string => mixed) $j
 */
function df_json_prettify($j):string {return df_json_encode(df_json_decode($j));}

/**
 * 2017-09-07
 * I use @uses df_is_assoc() check,
 * because otherwise @uses df_ksort_r_ci() will convert the numeric arrays to associative ones,
 * and their numeric keys will be ordered as strings.
 * @see df_json_dont_sort()
 * @used-by df_json_decode()
 * @used-by df_json_encode()
 * @param mixed $v
 * @return mixed
 */
function df_json_sort($v) {return !is_array($v) || !J::bSort() ? $v : df_ksort_r_ci($v);}