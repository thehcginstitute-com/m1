<?php
/**
 * 2017-07-09
 * @see df_dump()
 * @see df_kv_table()
 * @used-by Df\Qa\Failure\Error::preface()
 * @param array(string => string) $a
 */
function df_kv(array $a, int $pad = 0):string {return df_cc_n(df_map_k(df_clean($a), function($k, $v) use($pad) {return
	(!$pad ? "$k: " : df_pad("$k:", $pad)) . df_dump($v)
;}));}