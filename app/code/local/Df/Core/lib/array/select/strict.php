<?php
use Closure as F;

/**
 * 2022-11-27
 * 1) $onE is a function in @used-by \Df\PaypalClone\W\Event::statusT().
 * 2) $onE is a non-function custom value in many places, e.g. @used-by \Df\Payment\W\Strategy\ConfirmPending::_handle()
 * @see dftr()
 * @param string|int|null $k
 * @param F|bool|mixed $onE [optional]
 * @return array|mixed|null
 */
function dfa_strict(array $a, $k, $onE = true) {/** @var mixed $r */
	if (df_nes($k)) {
		if (true === $onE) {
			df_error('[dfa_strict] The key is required but is not provided.');
		}
		$r = null;
	}
	else {
		$r = dfa($a, $k);
		if (is_null($r) && true === $onE) {
			df_error("[dfa_strict] The array does not have a value for the key «{$k}».");
		}
	}
	return !is_null($r) ? $r : (!$onE instanceof F ? $onE : $onE());
}