<?php
/**
 * @param string|string[] $text
 * @return string|string[]
 */
function df_quote($text) {
	$quotes =['«', '»']; /** @var string[] $quotes */
	/**
	 * 2016-11-13
	 * Обратите внимание на красоту решения: мы «склеиваем кавычки»,
	 * используя в качестве промежуточного звена исходную строку.
	 * @param string $text
	 * @return string
	 */
	$f = function($text) use($quotes) {return implode($text, $quotes);};
	return !is_array($text) ? $f($text) : array_map($f, $text);
}