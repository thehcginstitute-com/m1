<?php
/**
 * @param string|object $c
 * @return string[]
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 */
function df_explode_class($c) {return df_explode_multiple(['\\', '_'], df_cts($c));}

/**
 * 2016-04-11 Dfe_CheckoutCom => [Dfe, Checkout, Com]
 * 2016-10-20
 * Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2024-01-11 "Port `df_explode_class_camel` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/197
 * @used-by df_explode_class_lc_camel()
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_camel($c):array {return dfa_flatten(df_explode_camel(explode('\\', df_cts($c))));}

/**
 * 2016-04-11
 * 2016-10-20
 * 1) Making $c optional leads to the error «get_class() called without object from outside a class»: https://3v4l.org/k6Hd5
 * 2) Dfe_CheckoutCom => [dfe, checkout, com]
 * 2024-01-11 "Port `df_explode_class_lc_camel` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/195
 * @used-by df_module_name_lc()
 * @param string|object $c
 * @return string[]
 */
function df_explode_class_lc_camel($c):array {return df_lcfirst(df_explode_class_camel($c));}

/**
 * 2021-02-24
 * @used-by df_caller_c()
 * @return string[]
 */
function df_explode_method(string $m):array {return explode('::', $m);}
