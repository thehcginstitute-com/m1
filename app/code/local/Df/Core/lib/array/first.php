<?php
/**
 * 2021-01-29
 * @used-by df_error_create()
 * @used-by dfa_try()
 * @used-by \HCG\MailChimp\Tags::address() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by IWD_OrderManager_Helper_Data::CheckTableEngine()
 * @param array $a
 * @return mixed|null
 */
function df_first(array $a) {return !$a ? null : reset($a);}

/**
 * Функция возвращает null, если массив пуст.
 * Если использовать @see end() вместо @see df_last(),
 * то указатель массива после вызова end сместится к последнему элементу.
 * При использовании @see df_last() смещения указателя не происходит,
 * потому что в @see df_last() попадает лишь копия массива.
 *
 * Обратите внимание, что неверен код
 *	$result = end($array);
 *	return (false === $result) ? null : $result;
 * потому что если @uses end() вернуло false, это не всегда означает сбой метода:
 * ведь последний элемент массива может быть равен false.
 * http://www.php.net/manual/en/function.end.php#107733
 * @param mixed[] $array
 * @return mixed|null
 */
function df_last(array $array) {return !$array ? null : end($array);}