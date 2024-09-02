<?php
/**
 * 2016-03-25 «charge.dispute.funds_reinstated» => [charge, dispute, funds, reinstated]
 * @used-by df_explode_class()
 * @used-by df_fe_name_short()
 * @param string[] $delimiters
 * @return string[]
 */
function df_explode_multiple(array $delimiters, string $s):array {
	$main = array_shift($delimiters); /** @var string $main */
	# 2016-03-25
	# «If `search` is an array and `replace` is a string, then this replacement string is used for every value of `search`.»
	# https://php.net/manual/function.str-replace.php
	return explode($main, str_replace($delimiters, $main, $s));
}

/**
 * 2018-04-24 I have added @uses trim() today.
 * @used-by df_module_enum()
 * @used-by df_parse_colon()
 * @used-by df_tab_multiline()
 * @used-by df_zf_http_last_req()
 * @return string[]
 */
function df_explode_n(string $s):array {return explode("\n", df_normalize(df_trim($s)));}

/**
 * 2016-09-03 Another implementation: df_explode_multiple(['/', DS], $path)
 * @used-by df_store_code_from_url()
 * @used-by df_url_trim_index()
 * @used-by \Df\Qa\Trace\Frame::url()
 * @return string[]
 */
function df_explode_path(string $p):array {return df_explode_xpath(df_path_n($p));}

/**
 * 2022-11-17
 * @used-by df_body_class()
 * @used-by df_file_name()
 * @used-by df_magento_version_remote()
 * @used-by df_phone_explode()
 * @used-by df_webserver()
 * @return string[]
 */
function df_explode_space(string $s):array {return
	# 2024-06-11
	# 1) "Improve `df_explode_space()`": https://github.com/mage2pro/core/issues/422
	# 2) `print_r(explode('a', 'baaab'));` → ['b', '', '', 'b'] https://3v4l.org/t6IBI
	# 3) `print_r(explode('a', 'a'))` → ['', ''] https://3v4l.org/u8pGS
	# «If separator values appear at the start or end of string,
	# said values will be added as an empty array value either in the first or last position of the returned array respectively.»
	# https://www.php.net/manual/en/function.explode.php#refsect1-function.explode-returnvalues
	df_clean(df_trim(explode(' ', $s)))
;}

/**
 * @return string[]
 */
function df_explode_url(string $url):array {return explode('/', $url);}

/**
 * 2015-02-06
 * Если разделитель отсутствует в строке, то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
 * Это вполне укладывается в наш универсальный алгоритм.
 * @used-by df_explode_path()
 * @used-by df_module_name_by_path()
 * @used-by dfa_deep()
 * @used-by dfa_deep_set()
 * @used-by dfa_deep_unset()
 * @param string|string[] $p
 * @return string[]
 */
function df_explode_xpath($p):array {return dfa_flatten(array_map(function($s) {return explode('/', $s);}, df_array($p)));}