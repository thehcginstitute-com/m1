<?php
/**
 * 2023-07-26 "Implement `dfa_has_keys()`": https://github.com/mage2pro/core/issues/258
 * 2024-01-11 "Port `dfa_has_keys` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/184
 * @used-by df_bt_entry_is_method()
 */
function dfa_has_keys(array $a, array $kk):bool {return count($kk) === count(dfa($a, $kk));}