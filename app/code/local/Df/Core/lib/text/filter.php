<?php
/**
 * http://darklaunch.com/2009/05/06/php-normalize-newlines-line-endings-crlf-cr-lf-unix-windows-mac
 * @used-by df_explode_n()
 * @param string $s
 * @return string
 */
function df_normalize($s) {return strtr($s, ["\r\n" => "\n", "\r" => "\n"]);}