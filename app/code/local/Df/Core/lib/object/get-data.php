<?php

use Varien_Object as _DO;

/**
 * 2020-02-04
 * @used-by df_assert_gd()
 * @used-by df_call()
 * @used-by \Df\Qa\Dumper::dumpObject()
 * @param mixed $v
 * @return bool
 */
function df_has_gd($v) {return $v instanceof _DO;}