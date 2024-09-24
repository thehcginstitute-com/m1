<?php
namespace Df\Qa;
/** 2023-07-25 @todo Use YAML instead of JSON for `df_dump()` https://github.com/mage2pro/core/issues/254 */
final class Dumper {
	/**
	 * @used-by df_dump()
	 * @used-by self::dumpArrayElements()
	 * @param mixed $v
	 */
	function dump($v):string {return is_object($v) ? $this->dumpObject($v) : (
		is_array($v) ? $this->dumpArray($v) : (is_bool($v) ? df_bts($v) : (is_string($v) ? $this->dumpS($v) : print_r($v, true)))
	);}

	/** @used-by self::dump() */
	private function dumpArray(array $a):string {return
        # 2023-07-25
        # "Return JSON from `\Df\Qa\Dumper::dumpArray()` for arrays without object elements":
        # https://github.com/mage2pro/core/issues/252
        !dfa_has_objects($a) ? df_json_encode($a) : "[\n" . df_tab($this->dumpArrayElements($a)) . "\n]"
    ;}

	/**
	 * Эта функция имеет 2 отличия от @see print_r():
	 * 1) она корректно обрабатывает объекты и циклические ссылки
	 * 2) она для верхнего уровня не печатает обрамляющее «Array()» и табуляцию, т.е. вместо
	 *		Array
	 *		(
	 *			[pattern_id] => p2p
	 *			[to] => 41001260130727
	 *			[identifier_type] => account
	 *			[amount] => 0.01
	 *			[comment] => Оплата заказа №100000099 в магазине localhost.com.
	 *			[message] =>
	 *			[label] => localhost.com
	 *		)
	 * выводит:
	 *	[pattern_id] => p2p
	 *	[to] => 41001260130727
	 *	[identifier_type] => account
	 *	[amount] => 0.01
	 *	[comment] => Оплата заказа №100000099 в магазине localhost.com.
	 *	[message] =>
	 *	[label] => localhost.com
	 * 2015-01-25 @uses df_ksort() для удобства сравнения двух версий массива/объекта в Araxis Merge.
	 * @see df_kv()
	 * @used-by self::dumpArray()
	 * @used-by self::dumpObject()
	 */
	private function dumpArrayElements(array $a):string {return df_cc_n(df_map_k(df_ksort($a), function($k, $v) {return
		"$k: {$this->dump($v)}"
	;}));}

	/**
	 * 2022-11-17
	 * `object` as an argument type is not supported by PHP < 7.2:
	 * https://github.com/mage2pro/core/issues/174#user-content-object
	 * 2024-06-03 We need to support PHP ≥ 7.1: https://github.com/mage2pro/core/issues/368
	 * @used-by self::dump()
	 * @param object $o
	 */
	private function dumpObject($o):string {/** @var string $r */
		/** @var string $hash */
		$c = get_class($o); /** @var string $c */
		if (isset($this->_dumped[$hash = spl_object_hash($o)])) {
			$r = "[recursion: $c]";
		}
		else {
			$this->_dumped[$hash] = true;
			/** @var string $v */
			$r = df_is_stringable($o)
				? sprintf("`$c::__toString()`%s",
					!df_is_multiline($v = df_string($o)) ? " «{$v}»" : df_n_prepend(df_tab($v))
				)
				: (
				# 2023-07-26
				# "`df_dump()` should handle `Traversable` similar to arrays": https://github.com/mage2pro/core/issues/253
				/** @var array(string => mixed)|null $a */
				is_null($a = df_has_gd($o) ? df_gd($o) : (is_iterable($o) ? df_ita($o) : null))
					? sprintf("$c: %s", df_json_encode_partial($o))
					: sprintf("$c(\n%s\n)", df_tab($this->dumpArrayElements($a)))
			);
		}
		return $r;
	}

	/**
	 * 2024-09-23
	 * @used-by self::dump()
	 */
	private function dumpS(string $s):string {return !df_check_json($r = df_trim(df_normalize($s))) ? $r : df_json_prettify($r);}

	/**
	 * @used-by self::dumpObject()
	 * @var array(string => bool)
	 */
	private $_dumped = [];

	/**
	 * Обратите внимание, что мы намеренно не используем для этого класса объект-одиночку,
	 * потому что нам надо вести учёт выгруженных объектов,
	 * чтобы не попасть в бесконечную рекурсию при циклических ссылках.
	 */
	static function i():self {return new self;}
}