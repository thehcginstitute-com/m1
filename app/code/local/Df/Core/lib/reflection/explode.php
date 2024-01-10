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
