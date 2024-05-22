<?php
namespace Df\Core;
use \Exception as E;
use \Throwable as Th; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
class Exception extends E {
	/**
	 * Обратите внимание, что PHP разрешает сигнатуре конструктора класса-потомка
	 * отличаться от сигнатуры конструктора класса родителя:
	 * http://3v4l.org/qQdJ3
	 * @used-by self::wrap()
	 * @used-by df_error_create()
	 * @param mixed ...$a
	 */
	function __construct(...$a) {
		$a0 = dfa($a, 0); /** @var string|E|array(string => mixed)|null $a0 */
		$prev = null; /** @var E|null $prev */
		$m = null;  /** @var string|null $m */
		# 2024-05-22
		# "Provide an ability to specify a context for a `Df\Core\Exception` instance":
		# https://github.com/mage2pro/core/issues/375
		if (df_is_assoc($a0)) {
			$this->context($a0);
		}
		elseif (is_string($a0)) {
			$m = $a0;
		}
		elseif (df_is_th($a0)) {
			$prev = df_th2x($a0);
		}
		elseif (is_string($a0)) {
			$m = __($a0);
		}
		$a1 = dfa($a, 1); /** @var int|string|E|null $a1 */
		if (null !== $a1) {
			if (df_is_th($a1)) {
				$prev = $a1;
			}
			elseif (is_int($prev)) {
				$this->_stackLevelsCountToSkip = $a1;
			}
		}
		if (null == $m) {
			$m = __($prev ? df_xts($prev) : 'No message');
			# 2017-02-20 To faciliate the «No message» diagnostics.
			if (!$prev) {
				df_bt_log();
			}
		}
		parent::__construct($m, 0, $prev);
	}

	/**
	 * 2014-05-20
	 * "Provide an ability to specify a context for a `Df\Core\Exception` instance": https://github.com/mage2pro/core/issues/375
	 * @used-by df_error_create()
	 * @used-by df_sentry()
	 * @used-by self::__construct()
	 * @used-by \Df\API\Response\Validator::__construct()
	 * @used-by \Df\Core\Exception::wrap()
	 * @used-by \Df\Qa\Failure\Exception::postface()
	 * @return self|array(string => mixed)
	 */
	function context($v = DF_N) {return df_prop($this, $v, []);}

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
	 * Цель этого метода — предоставить потомкам возможность
	 * указывать тип предыдущей исключительной ситуации в комментарии PHPDoc для потомка.
	 * Метод @uses E::getPrevious() объявлен как final,
	 * поэтому потомки не могут в комментариях PHPDoc указывать его тип: IntelliJ IDEA ругается.
	 */
	protected function prev():E {return $this->getPrevious();}

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