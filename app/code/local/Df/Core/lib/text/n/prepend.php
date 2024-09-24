<?php
/**
 * 2015-12-25
 * @used-by Df\Qa\Dumper::dumpObject()
 */
function df_n_prepend(string $s):string {return df_es($s) ? $s : "\n$s";}