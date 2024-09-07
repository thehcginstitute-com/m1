<?php
use Varien_Object as _DO;

/**
 * 2020-01-29
 * 2024-05-14 "Port `dfad()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/603
 * @used-by df_call()
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return _DO|mixed
 */
function dfad(_DO $o, $k = '', $d = null) {return df_nes($k) ? $o : dfa(df_gd($o), $k, $d);}