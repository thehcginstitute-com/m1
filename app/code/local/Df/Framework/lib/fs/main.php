<?php
use Df\Framework\Fs;

/**
 * 2015-11-29
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $contents will be written on a new line.
 * 2024-01-11 "Port `df_file_write` from `rm3`": https://github.com/thehcginstitute-com/m1/issues/167
 * @used-by df_report()
 */
function df_file_write(string $p, string $contents, bool $append = false):void {
	Fs::createAndMakeWritable($p);
	$abs = BP . "/$p"; /** #var string $abs */
	$append = $append && file_exists($abs) && 0 !== filesize($abs);
	/**
	 * 2018-07-06
	 * Note 1. https://stackoverflow.com/a/4857194
	 * Note 2.
	 * @see ftell() and @see \Magento\Framework\Filesystem\File\Read::tell() do not work here
	 * even if the file is opened in the `a+` mode:
	 * https://php.net/manual/function.ftell.php#116885
	 * «When opening a file for reading and writing via fopen('file','a+')
	 * the file pointer should be at the end of the file.
	 * However ftell() returns int(0) even if the file is not empty.»
	 */
	if ($append) {
		$contents = PHP_EOL . $contents; # 2018-07-06 «PHP fwrite new line» https://stackoverflow.com/a/15130410
	}
	/** @var int|bool $r */
	$r = file_put_contents($p, $contents, $append ? FILE_APPEND : 0);
	df_assert(false !== $r);
}