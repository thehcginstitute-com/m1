<?php
/**
 * 2016-11-08
 * Отличия этой функции от @uses array_filter():
 * 1) работает не только с массивами, но и с @see Traversable
 * 2) принимает аргументы в произвольном порядке.
 * Третий параметр — $flag — намеренно не реализовал,
 * потому что вроде бы для @see Traversable он особого смысла не имеет,
 * а если у нас гарантирвоанно не @see Traversable, а ассоциативный массив,
 * то мы можем использовать array_filter вместо df_filter.
 * 2020-02-05 Now it correcly handles non-associative arrays.
 * 2024-03-03 "Port `df_filter()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/452
 * @used-by df_clean_r()
 * @param callable|array(int|string => mixed)|array[]Traversable $a1
 * @param callable|array(int|string => mixed)|array[]|Traversable $a2
 * @return array(int|string => mixed)
 */
function df_filter($a1, $a2):array {return df_filter_f($a1, $a2, 'array_filter');}