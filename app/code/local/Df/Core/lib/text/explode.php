<?php
/**
 * 2016-03-25 «charge.dispute.funds_reinstated» => [charge, dispute, funds, reinstated]
 * @param string[] $delimiters
 * @param string $s
 * @return string[]
 */
function df_explode_multiple(array $delimiters, $s) {
	$main = array_shift($delimiters); /** @var string $main */
	/**
	 * 2016-03-25
	 * «If search is an array and replace is a string,
	 * then this replacement string is used for every value of search.»
	 * http://php.net/manual/function.str-replace.php
	 */
	return explode($main, str_replace($delimiters, $main, $s));
}

/**
 * 2018-04-24 I have added @uses trim() today.
 * @used-by df_module_enum()
 * @used-by df_parse_colon()
 * @used-by df_tab_multiline()
 * @param string $s
 * @return string[]
 */
function df_explode_n($s) {return explode("\n", df_normalize(df_trim($s)));}

/**
 * 2022-11-17
 * @used-by df_file_name()
 * @used-by df_magento_version_remote()
 * @used-by df_phone_explode()
 * @used-by df_webserver()
 * @param string $s
 * @return string[]
 */
function df_explode_space($s) {return df_trim(explode(' ', $s));}

/**
 * 2015-02-06
 * Если разделитель отсутствует в строке, то @uses explode() вернёт не строку, а массив со одим элементом — строкой.
 * Это вполне укладывается в наш универсальный алгоритм.
 * @used-by df_explode_path()
 * @used-by dfa_deep()
 * @used-by dfa_deep_set()
 * @used-by dfa_deep_unset()
 * @param string|string[] $p
 * @return string[]
 */
function df_explode_xpath($p) {return dfa_flatten(array_map(function($s) {return explode('/', $s);}, df_array($p)));}