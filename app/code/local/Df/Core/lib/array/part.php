<?php
/**
 * 2021-10-05, 2021-11-30
 * @uses array_slice() returns an empty array if `$limit` is `0`, and returns all elements if `$limit` is `null`,
 * so I convert `0` and other empty values to `null`.
 * @used-by df_bt()
 * @used-by df_product_images_additional()
 * @param int $offset
 * @param int $length [optional]
 * @return array
 */
function df_slice(array $a, $offset, $length = 0) {return array_slice($a, $offset, df_etn($length));}

/**
 * Отсекает первый элемент массива и возвращает хвост (аналог CDR в Lisp).
 * Обратите внимание, что если исходный массив содержит меньше 2 элементов, то функция вернёт пустой массив.
 * 2024-02-03 "Port `df_tail()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/351
 * @see df_first()
 * @see df_last()
 */
function df_tail(array $a):array {return array_slice($a, 1);}