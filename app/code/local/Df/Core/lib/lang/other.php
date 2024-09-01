<?php
/**
 * 2024-03-03 "Refactor `static $inProcess` to a function": https://github.com/mage2pro/core/issues/354
 * @see df_prop()
 * @see dfc()
 * @see dfcf()
 * @used-by df_sprintf_strict()
 * @used-by \Df\Qa\Failure\Error::log()
 */
function df_no_rec(Closure $f):void {
	static $inProcess = []; /** @var bool[] $inProcess */
	$k = df_caller_mf(); /** @var string $k */
	if (!isset($inProcess[$k])) {
		$inProcess[$k] = true;
		try {$f();}
		finally {unset($inProcess[$k]);}
	}
}

/**
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @param mixed $v
 * @return mixed
 */
function df_nop($v) {return $v;}