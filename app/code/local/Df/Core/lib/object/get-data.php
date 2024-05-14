<?php
use Closure as F;
use Varien_Object as _DO;

/**
 * 2020-02-04
 * 2024-05-14 "Port `df_gd()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/604
 * @used-by dfad()
 * @used-by dfa_remove_objects()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param mixed|_DO $v
 * @param F|bool|mixed $onE [optional]
 * @return array(string => mixed)
 */
function df_gd($v, $onE = true):array {return df_try(function() use($v) {return $v->getData();}, $onE);}

/**
 * 2020-02-04
 * @used-by df_assert_gd()
 * @used-by df_call()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param mixed $v
 * @return bool
 */
function df_has_gd($v) {return $v instanceof _DO;}