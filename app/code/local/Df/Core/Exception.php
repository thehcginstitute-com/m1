<?php
namespace Df\Core;
use \Exception as E;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
class Exception extends E implements \ArrayAccess {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @used-by self::wrap()
	 * @param mixed ...$args
	 */
	function __construct(...$args) {
		$arg0 = dfa($args, 0); /** @var string|E|array(string => mixed)|null $arg0 */
		$prev = null; /** @var E|null $prev */
		$m = null;  /** @var string|null $m */
		# 2015-10-10
		if (is_array($arg0)) {
			$this->_data = $arg0;
		}
		elseif (is_string($arg0)) {
			$m = $arg0;
		}
		elseif (df_is_th($arg0)) {
			$prev = df_th2x($arg0);
		}
		elseif (is_string($arg0)) {
			$m = __($arg0);
		}
		$arg1 = dfa($args, 1); /** @var int|string|E|null $arg1 */
		if (!is_null($arg1)) {
			if (df_is_th($arg1)) {
				$prev = $arg1;
			}
			elseif (is_int($prev)) {
				$this->_stackLevelsCountToSkip = $arg1;
			}
			elseif (is_string($arg1)) {
				$this->comment((string)$arg1);
			}
		}
		if (is_null($m)) {
			$m = __($prev ? df_xts($prev) : 'No message');
			# 2017-02-20 To facilite the «No message» diagnostics.
			if (!$prev) {
				df_bt_log();
			}
		}
		parent::__construct($m, 0, $prev);
	}

	/**
	 * @used-by self::__construct()
	 * @param mixed ...$a
	 */
	final function comment(...$a):void {$this->_comments[]= df_format($a);}

	/**
	 * @used-by \Df\Qa\Failure\Exception::preface()
	 * @return string[]
	 */
	final function comments():array {return $this->_comments;}

	/**
	 * @used-by \Df\Qa\Failure\Exception::stackLevel()
	 */
	final function getStackLevelsCountToSkip():int {return $this->_stackLevelsCountToSkip;}

	/**
	 * 2016-07-31
	 * @used-by df_error_html()
	 */
	final function markMessageAsHtml():self {$this->_messageIsHtml = true; return $this;}

	/**
	 * Стандартный метод @see E::getMessage() объявлен как final.
	 * Чтобы метод для получения диагностического сообщения
	 * можно было переопределять — добавляем свой.
	 *
	 * 2015-02-22
	 * Конечно, наша архитектура обладает тем недостатком,
	 * что пользователи нашего класса и его потомков должны для извлечения диагностического сообщения
	 * вместо стандартного интерфейса @see E::getMessage()
	 * использовать функцию @see df_xts()
	 *
	 * Однако неочевидно, как обойти этот недостаток.
	 * В частности, способ, когда диагностическое сообщение формируется прямо в конструкторе
	 * и передается первым параметром родительскому конструктору @see E::__construct()
	 * не всегда подходит, потому что полный текст диагностического сообщения
	 * не всегда известен в момент вызова конструктора @see __construct().
	 * Пример, когда неизвестен: @see \Df\Core\Exception_Batch::message()
	 * (тот класс работает как контеёнер для других исключительных ситуаций,
	 * и полный текст диагностического сообщения
	 * получается объединением текстов элементом контейнера,
	 * которые добавляются динамически, а не сразу в конструкторе).
	 * По этой причине данный метод @see message(), несмотря на его некую громоздкость,
	 * нам действительно нужен.
	 * @used-by df_xts()
	 */
	function message():string {return $this->getMessage();}

	/**
	 * Сообщение для разработчика.
	 * @used-by df_xtsd()
	 * @used-by self::messageSentry()
	 * @used-by \Df\Qa\Failure\Exception::main()
	 */
	function messageD():string {return $this->message();}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetExists()
	 * @param string $offset
	 */
	function offsetExists($offset):bool {return isset($this->_data[$offset]);}

	/**
	 * 2015-10-10
	 * 2022-10-24
	 * 1) `mixed` as a return type is not supported by PHP < 8:
	 * https://github.com/mage2pro/core/issues/168#user-content-mixed
	 * 2) `ReturnTypeWillChange` allows us to suppress the return type absence notice:
	 * https://github.com/mage2pro/core/issues/168#user-content-absent-return-type-deprecation
	 * https://github.com/mage2pro/core/issues/168#user-content-returntypewillchange
	 * @override
	 * @see \ArrayAccess::offsetGet()
	 * @param string $offset
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	function offsetGet($offset) {return dfa($this->_data, $offset);}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetSet()
	 * @param string $offset
	 * @param mixed $value
	 */
	function offsetSet($offset, $value):void {$this->_data[$offset] = $value;}

	/**
	 * 2015-10-10
	 * @override
	 * @see \ArrayAccess::offsetUnset()
	 * @param string $offset
	 */
	function offsetUnset($offset):void {unset($this->_data[$offset]);}

	/**
	 * Цель этого метода — предоставить потомкам возможность
	 * указывать тип предыдущей исключительной ситуации в комментарии PHPDoc для потомка.
	 * Метод @uses E::getPrevious() объявлен как final,
	 * поэтому потомки не могут в комментариях PHPDoc указывать его тип: IntelliJ IDEA ругается.
	 */
	protected function prev():E {return $this->getPrevious();}

	/**
	 * @used-by self::comments()
	 * @var string[]
	 */
	private $_comments = [];

	/**
	 * 2015-10-10
	 * @used-by self::__construct()
	 * @used-by self::offsetExists()
	 * @used-by self::offsetGet()
	 * @used-by self::offsetUnset()
	 * @var array(string => mixed)
	 */
	private $_data = [];

	/**
	 * 2016-07-31
	 * @used-by self::isMessageHtml()
	 * @used-by self::markMessageAsHtml()
	 * @var bool
	 */
	private $_messageIsHtml = false;

	/**
	 * Количество последних элементов стека вызовов,
	 * которые надо пропустить как несущественные при показе стека вызовов в диагностическом отчёте.
	 * Это значение становится положительным,
	 * когда исключительная ситуация возбуждается не в момент её возникновения,
	 * а в некоей вспомогательной функции-обработчике, вызываемой в сбойном участке:
	 * @var int
	 */
	private $_stackLevelsCountToSkip = 0;

	/**
	 * 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
	 * @used-by df_error_create()
	 * @used-by \Df\Qa\Failure\Exception::i()
	 */
	final static function wrap(Th $th):self {return $th instanceof self ? $th : new self($th);}
}