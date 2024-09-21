<?php
use Varien_Object as _DO;

/**
 * 2017-01-12
 * 1) PHP, к сожалению, не разрешает в выражении с `new` делать выражением имя класса: https://3v4l.org/U6TJR
 * Поэтому и создал эту небольшую функцию.
 * В отличие от @see df_new_om(), она не использует Object Manager.
 * 2) Впервые использую в своём коде возможность argument unpacking, появившуюся в PHP 5.6:
 * https://3v4l.org/eI2vf
 * http://stackoverflow.com/a/25781989
 * https://php.net/manual/functions.arguments.php#example-145
 * 2022-10-31 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
 * @used-by df_newa()
 * @param mixed ...$a
 * @return object
 */
function df_new(string $c, ...$a) {return new $c(...$a);}

/**
 * 2017-01-12
 * PHP, к сожалению, не разрешает в выражении с new делать выражением имя класса.
 * Поэтому и создал эту небольшую функцию.
 * В отличие от @see df_new_om(), она не использует Object Manager.
 * 2022-10-31 `object` as a return type is not supported by PHP < 7.2: https://3v4l.org/dAmcs
 * @used-by dfs_con()
 * @used-by \Df\Zf\Validate\StringT\Parser::getZendValidator()
 * @param mixed ...$a
 * @return object
 */
function df_newa(string $c, string $expected = '', ...$a) {return df_ar(df_new($c, ...$a), $expected);}