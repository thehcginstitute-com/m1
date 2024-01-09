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
 * @return string
 */
function df_dump($v) {return Dumper::i()->dump($v);}