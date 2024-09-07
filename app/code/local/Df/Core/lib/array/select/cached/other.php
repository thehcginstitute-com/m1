<?php
use Closure as F;

/**
 * 2020-01-29
 * @see dfaoc()
 * @used-by df_credentials()
 * @used-by dfe_portal_module()
 * @used-by \Df\Framework\Request::extra()
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfac(F $f, $k = '', $d = null) {return dfa(dfcf($f, [], [], false, 1), $k, $d);}

/**
 * 2020-01-29
 * 2022-11-17
 * `object` as an argument type is not supported by PHP < 7.2: https://github.com/mage2pro/core/issues/174#user-content-object
 * 2024-06-03 We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
 * @see dfac()
 * @used-by \Df\Core\Visitor::r()
 * @param object $o
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfaoc($o, F $f, $k = '', $d = null) {return dfa(dfc($o, $f, [], false, 1), $k, $d);}