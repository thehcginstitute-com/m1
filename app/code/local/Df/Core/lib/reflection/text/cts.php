<?php
/**
 * 2015-08-14
 * Обратите внимание, что @uses get_class() не ставит «\» впереди имени класса:
 * http://3v4l.org/HPF9R
 *	namespace A;
 *	class B {}
 *	$b = new B;
 *	echo get_class($b);
 * => «A\B»
 *
 * 2015-09-01
 * Обратите внимание, что @uses ltrim() корректно работает с кириллицей:
 * https://3v4l.org/rrNL9
 * echo ltrim('\\Путь\\Путь\\Путь', '\\');  => Путь\Путь\Путь
 *
 * 2016-10-20
 * Нельзя делать параметр $c опциональным, потому что иначе получим сбой:
 * «get_class() called without object from outside a class»
 * https://3v4l.org/k6Hd5
 *
 * @param string|object $c
 * @param string $del [optional]
 * @return string
 */
function df_cts($c, $del = '\\') {
	$r = is_object($c) ? get_class($c) : ltrim($c, '\\'); /** @var string $r */
	return '\\' === $del ? $r : str_replace('\\', $del, $r);
}