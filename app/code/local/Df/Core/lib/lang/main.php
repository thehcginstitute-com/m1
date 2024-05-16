<?php
use Closure as F;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
/**
 * 2016-02-09 Осуществляет ленивое ветвление только для первой ветки.
 * @used-by df_leaf()
 * @used-by df_request()
 * @param bool $cond
 * @param mixed|callable $onTrue
 * @param mixed|null $onFalse [optional]
 * @return mixed
 */
function df_if1($cond, $onTrue, $onFalse = null) {return $cond ? df_call_if($onTrue) : $onFalse;}

/**
 * 2017-04-15
 * @used-by df_country_ctn()
 * @param F $try
 * @param F|T|bool|mixed $onE [optional]
 * @return mixed
 * @throws T
 */
function df_try(F $try, $onE = null) {
	try {return $try();}
	catch(T $t) {return $onE instanceof F ? $onE($t) : (df_is_th($onE) || true === $onE ? df_error($t) : $onE);}
}