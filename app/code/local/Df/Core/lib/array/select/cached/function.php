<?php
use Closure as F;

/**
 * 2020-01-29
 * @see dfaoc()
 * @param string|string[] $k [optional]
 * @param mixed|callable|null $d [optional]
 * @return mixed
 */
function dfac(F $f, $k = '', $d = null) {return dfa(dfcf($f, [], [], false, 1), $k, $d);}