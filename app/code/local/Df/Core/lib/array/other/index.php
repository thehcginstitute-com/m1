<?php
use Varien_Object as _DO;

/**
 * 2015-12-30 Преобразует коллекцию или массив в карту.
 * 2024-05-14 "Port `df_index()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/600
 * 2024-06-03
 * 1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 3) https://php.net/manual/en/language.types.iterable.php
 * https://3v4l.org/AOWmO
 * @used-by df_mvars()
 * @param string|Closure $k
 * @param Traversable|array(int|string => _DO) $a
 */
function df_index($k, iterable $a):array {return array_combine(df_column($a, $k), df_ita($a));}
