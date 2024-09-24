<?php
use Df\Core\Helper\Text as T;
/**
 * 2015-12-31
 * 1) IntelliJ IDEA этого не показывает, но пробел здесь не обычный, а узкий: https://en.wikipedia.org/wiki/Thin_space
 * 2) Глобальные константы появились в PHP 5.3:
 * http://www.codingforums.com/php/303927-unexpected-t_const-php-version-5-2-17-a.html
 */
const DF_THIN_SPACE = ' ';

/**
 * https://php.net/manual/function.str-split.php#107658
 * 2022-10-31 @deprecated It is unused.
 * @return string[]
 */
function df_string_split(string $s):array {return preg_split("//u", $s, -1, PREG_SPLIT_NO_EMPTY);}

function df_strings_are_equal_ci(string $s1, string $s2):bool {return 0 === strcmp(mb_strtolower($s1), mb_strtolower($s2));}

/**
 * @used-by df_quote_double()
 * @used-by df_quote_russian()
 * @used-by df_quote_single()
 */
function df_t():T {return T::s();}

/**
 * 2016-07-05 $length - это длина уникальной части, без учёта $prefix.
 */
function df_uid(int $length = 0, string $prefix = ''):string {
	# Важно использовать $more_entropy = true, потому что иначе на быстрых серверах
	# (я заметил такое поведение при использовании Zend Server Enterprise и PHP 5.4)
	# uniqid будет иногда возвращать одинаковые значения при некоторых двух последовательных вызовах.
	# 2016-07-05
	# При параметре $more_entropy = true значение будет содержать точку,
	# например: «4b340550242239.64159797».
	# Решил сегодня удалять эту точку из-за платёжной системы allPay,
	# которая требует, чтобы идентификаторы содержали только цифры и латинские буквы.
	$r = str_replace('.', '', uniqid($prefix, true)); /** @var string $r */
	# Уникальным является именно окончание uniqid, а не начало.
	# Два последовательных вызова uniqid могу вернуть:
	# 5233061890334
	# 52330618915dd
	# Начало у этих значений — одинаковое, а вот окончание — различное.
	return $prefix . (!$length ? $r : substr($r, -$length));
}