<?php
/**
 * 2015-12-29
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 * @param string|object $c
 * @return string
 */
function df_class_l($c) {return df_last(df_explode_class($c));}

/**
 * 2018-01-30
 * @param string|object $c
 * @return string
 */
function df_class_llc($c) {return strtolower(df_class_l($c));}