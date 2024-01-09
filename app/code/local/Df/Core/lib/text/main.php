<?php
/**
 * @see df_bts_yn()
 * @used-by \Df\Qa\Dumper::dump()
 * @param bool $v
 * @return string
 */
function df_bts($v) {return $v ? 'true' : 'false';}

/**
 * 2015-04-17
 * Добавлена возможность указывать в качестве $needle массив.
 * Эта возможность используется в\AttributeFilter::parse()
 * @param string $haystack
 * @param string $n
 * @return bool
 * Я так понимаю, здесь безопасно использовать @uses strpos вместо @see mb_strpos() даже для UTF-8.
 * http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 */
function df_contains($haystack, $n) {return false !== strpos($haystack, $n);}

/**
 * @param mixed|null $v
 * @return mixed
 */
function df_nts($v) {return !is_null($v) ? $v : '';}