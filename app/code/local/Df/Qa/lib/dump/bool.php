<?php
/**
 * @see df_bts_yn()
 * @used-by \Df\Qa\Dumper::dump()
 */
function df_bts(bool $v):string {return $v ? 'true' : 'false';}

/**
 * 2017-11-08
 * @see df_bts()
 */
function df_bts_yn(bool $v):string {return $v ? 'yes' : 'no';}