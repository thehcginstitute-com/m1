<?php
/**
 * 2016-07-31
 * 2024-06-03
 * 1.1) "Use the `iterable` type": https://github.com/mage2pro/core/issues/403
 * 1.2) `iterable` is supported by PHP ≥ 7.1: https://3v4l.org/qNX1j
 * 1.3) https://php.net/manual/en/language.types.iterable.php
 * 2) "Port `dfa_ids()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/635
 * @uses df_id()
 * @used-by hcg_mc_stores() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @param Traversable|array(int|string => Varien_Object) $c
 * @return int[]|string[]
 */
function dfa_ids(iterable $c):array {return df_map('df_id', $c);}