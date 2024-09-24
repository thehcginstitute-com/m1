<?php
use Df\Core\Helper\Text as T;

/**
 * @see df_bts_yn()
 * @used-by \Df\Qa\Dumper::dump()
 * @used-by \IWD_OrderManager_Helper_Data::CheckTableEngine()
 */
function df_bts(bool $v):string {return $v ? 'true' : 'false';}

/**
 * 2015-04-17
 * Добавлена возможность указывать в качестве $needle массив.
 * Эта возможность используется в\AttributeFilter::parse()
 * @used-by Mage::printException()
 * @param string $haystack
 * @param string $n
 * @return bool
 * Я так понимаю, здесь безопасно использовать @uses strpos вместо @see mb_strpos() даже для UTF-8.
 * http://stackoverflow.com/questions/13913411/mb-strpos-vs-strpos-whats-the-difference
 */
function df_contains($haystack, $n) {return false !== strpos($haystack, $n);}

/**
 * @used-by df_quote_double()
 * @used-by df_quote_russian()
 * @used-by df_quote_single()
 * @used-by \Df\Core\Text\Regex::isSubjectMultiline()
 */
function df_t():T {return T::s();}

/**
 * 2024-05-14 "Port `df_string_debug()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/608
 * @used-by \Df\Zf\Validate::message()
 * @param mixed $v
 */
function df_string_debug($v):string {
	$r = ''; /** @var string $r */
	if (is_object($v)) {
		/**
		 * 2016-09-04
		 * К сожалению, нельзя здесь для проверки публичности метода использовать @see is_callable(),
		 * потому что наличие @see Varien_Object::__call() приводит к тому, что `is_callable` всегда возвращает `true`.
		 * @uses method_exists(), в отличие от `is_callable`, не гарантирует публичную доступность метода:
		 * т.е. метод может у класса быть, но вызывать его всё равно извне класса нельзя,
		 * потому что он имеет доступность `private` или `protected`.
		 * Пока эта проблема никак не решена.
		 */
		if (!method_exists($v, '__toString')) {
			$r = get_class($v);
		}
	}
	elseif (is_array($v)) {
		$r = sprintf('<an array of %d elements>', count($v));
	}
	elseif (is_bool($v)) {
		$r = $v ? 'logical <yes>' : 'logical <no>';
	}
	else {
		$r = strval($v);
	}
	return $r;
}