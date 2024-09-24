<?php
use Df\Core\Text\Regex as R;

/**
 * @return int|null|bool
 */
function df_preg_int(string $pattern, string $subject, bool $throwOnNotMatch = false) {return R::i(
	$pattern, $subject, true, $throwOnNotMatch
)->matchInt();}

/**
 * 2015-03-23 Добавил поддержку нескольких пар круглых скобок (в этом случае функция возвращает массив).
 * @used-by df_preg_prefix()
 * @used-by df_xml_parse_header()
 * @return string|string[]|null|bool
 */
function df_preg_match(string $pattern, string $subject, bool $throwOnNotMatch = false) {return R::i(
	$pattern, $subject, true, $throwOnNotMatch
)->match();}

/**
 * 2018-11-11
 * @return int|null|bool
 */
function df_preg_prefix(string $prefix, string $subject, bool $throwOnNotMatch = false) {return df_preg_match(
	sprintf('#^%s([\S\s]*)#', preg_quote($prefix)), $subject, $throwOnNotMatch
);}

/**
 * 2022-10-31 @deprecated It is unused.
 * @throws Exception
 */
function df_preg_test(string $pattern, string $subject, bool $throwOnError = true):bool {return R::i(
	$pattern, $subject, $throwOnError, false
)->test();}