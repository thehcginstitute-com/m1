<?php

use Df\Qa\Dumper;
use Varien_Object as _DO;

/**
 * Обратите внимание, что мы намеренно не используем для @uses Df_Core_Dumper
 * объект-одиночку, потому что нам надо вести учёт выгруженных объектов,
 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
 * @see df_type()
 * @used-by df_assert_eq()
 * @used-by df_bool()
 * @used-by df_extend()
 * @used-by df_sentry()
 * @used-by df_type()
 * @used-by dfc()
 * @used-by dfs_con()
 * @param _DO|mixed[]|mixed $v
 * @param mixed $v
 */
function df_dump($v):string {return Dumper::i()->dump($v);}

/**
 * 2023-08-04
 * 2024-01-11 "Port `df_dump_ds` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/178
 * @used-by df_log_l()
 * @used-by \Df\Qa\Failure\Exception::postface()
 */
function df_dump_ds($v):string {return df_json_dont_sort(function() use($v):string {return df_dump($v);});}

/**
 * 2015-04-05
 * 2022-10-14 @see get_debug_type() has been added to PHP 8: https://php.net/manual/function.get-debug-type.php
 * 2024-03-05 "Port `df_type()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/460
 * @see df_dump()
 * @used-by df_ar()
 * @used-by df_assert_gd()
 * @used-by df_assert_iterable()
 * @used-by df_customer()
 * @used-by df_oq_currency_c()
 * @used-by df_order()
 * @used-by df_result_s()
 * @used-by dfaf()
 * @used-by dfpex_args()
 * @param mixed $v
 */
function df_type($v):string {return is_object($v) ? sprintf('an object: %s', get_class($v), df_dump($v)) : (is_array($v)
	? (10 < ($c = count($v)) ? "«an array of $c elements»" : 'an array: ' . df_dump($v))
	/** 2020-02-04 We should not use @see df_desc() here */
	: (is_null($v) ? '`null`' : sprintf('«%s» (%s)', df_string($v), gettype($v)))
);}