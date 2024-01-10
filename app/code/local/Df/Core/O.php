<?php
namespace Df\Core;
/**
 * 2017-07-13
 * 2024-01-10 "Port `Df\Core\O` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/155
 * @see \Df\Qa\Trace\Frame
 */
class O implements \ArrayAccess {
	/**
	 * 2017-07-13
	 * @used-by \Df\Qa\Failure\Error::i()
	 * @used-by \Df\Qa\Failure\Exception::i()
	 * @param array(string => mixed) $a [optional]
	 */
	final function __construct(array $a = []) {$this->_a = $a;}

	/**
	 * 2017-07-13
	 * @param string|string[] $k [optional]
	 * @param string|null $d [optional]
	 * @return array(string => mixed)|mixed|null
	 */
	function a($k = '', $d = null) {return \dfa($this->_a, $k, $d);}

	/**
	 * 2023-08-25
	 */
	function isEmpty():bool {return !$this->_a;}

	/**
	 * 2017-07-13
	 */
	function j():string {return \df_json_encode($this->_a);}

	/**
	 * 2017-07-13
	 * «This method is executed when using isset() or empty() on objects implementing ArrayAccess.
	 * When using empty() ArrayAccess::offsetGet() will be called and checked if empty
	 * only if ArrayAccess::offsetExists() returns TRUE».
	 * https://php.net/manual/arrayaccess.offsetexists.php
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @used-by df_prop()
	 * @param string $k
	 */
	function offsetExists($k):bool {return !is_null(dfa_deep($this->_a, $k));}

	/**
	 * 2017-07-13
	 * 2022-10-24
	 * 1) `mixed` as a return type is not supported by PHP < 8:
	 * https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * 2) `ReturnTypeWillChange` allows us to suppress the return type absence notice:
	 * https://github.com/mage2pro/core/issues/168#user-content-absent-return-type-deprecation
	 * https://github.com/mage2pro/core/issues/168#user-content-returntypewillchange
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @used-by df_prop()
	 * @param string $k
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	function offsetGet($k) {return \dfa_deep($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @used-by df_prop()
	 * @param string $k
	 * @param mixed $v
	 */
	function offsetSet($k, $v):void {dfa_deep_set($this->_a, $k, $v);}

	/**
	 * 2017-07-13
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $k
	 */
	function offsetUnset($k):void {dfa_deep_unset($this->_a, $k);}

	/**
	 * 2017-07-13
	 * @used-by self::__construct()
	 * @used-by self::a()
	 * @used-by self::isEmpty()
	 * @var array(string => mixed)
	 */
	private $_a;
}