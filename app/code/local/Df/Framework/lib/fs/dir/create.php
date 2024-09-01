<?php
use Df\Framework\Fs as Fs;

/**
 * 2021-03-20
 * @used-by df_file_write()
 * @throws Exception
 */
function df_mkdir(string $f):void {Fs::createAndMakeWritable($f);}