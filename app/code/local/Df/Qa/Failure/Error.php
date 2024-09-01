<?php
namespace Df\Qa\Failure;
final class Error extends \Df\Qa\Failure {
	/**
	 * 2015-04-04
	 * 2022-12-14 PHP supports "{$this::t()}" (unlike "{$this->t()}") since PHP 5.3: https://3v4l.org/HBFTd
	 * @override
	 * @see \Df\Qa\Failure::main()
	 * @used-by \Df\Qa\Failure::report()
	 * @return string
	 */
	protected function main() {return "[{$this::type(true)}] {$this::msg()}";}

	/**
	 * 2020-09-25 "Enrich data logged by my `register_shutdown_function` handler": https://github.com/mage2pro/core/issues/144
	 * @override
	 * @see \Df\Qa\Failure::preface()
	 * @used-by \Df\Qa\Failure::report()
	 * @used-by self::report()
	 * @return string
	 */
	protected function preface() {return df_kv(
		df_context() + ['File' => df_path_rel($this::info('file')), 'Line' => $this::info('line')]
	);}

	/**
	 * @override
	 * @see \Df\Qa\Failure::stackLevel()
	 * @used-by \Df\Qa\Failure::postface()
	 * @return int
	 */
	protected function stackLevel() {return 13;}

	/**
	 * @see debug_backtrace() не работает в функции-обработчике
	 * @see register_shutdown_function()
	 * Однако @uses xdebug_get_function_stack() — работает.
	 * @override
	 * @see \Df\Qa\Failure::trace()
	 * @used-by \Df\Qa\Failure::postface()
	 * @return array(array(string => string|int))
	 */
	protected function trace() {return self::xdebug() ? array_reverse(xdebug_get_function_stack()) : [];}

	/**
	 * @used-by self::check()
	 * @throws \Exception
	 */
	private function log():void {
		# 2015-04-04
		# Нам нужно правильно обработать ситуацию, когда при формировании диагностического отчёта о сбое происходит новый сбой.
		# 1) Статическая переменная `$inProcess` предотвращает нас от бесконечной рекурсии.
		# 2) try... catch позволяет нам перехватить внутренний сбой,
		# сформировать диагностическое сообщение о нём, а затем перевозбудить его снова, чтобы вывести на экран.
		# Обратите внимание, что внутренний сбой не будет виден на экране при асинхронном запросе
		# (много таких запросов делает, например, страница оформления заказа),
		# поэтому try... catch с целью записи отчёта крайне важно:
		# без этого при сбое асинхроноого запроса диагностичекское сообщение о сбое окажется утраченным.
		static $inProcess;
		if (!$inProcess) {
			$inProcess = true;
			try {
				df_report('mage2.pro/{date}--{time}.log', $this->report());
				$inProcess = false;
			}
			catch (\Exception $e) {
				df_log(df_xts($e));
				throw $e;
			}
		}
	}

	/**
	 * 2015-04-05 Оборачиваем код в try..catch, чтобы не утратить сообщение о внутреннем сбое при асинхронном запросе.
	 * @used-by https://github.com/mage2pro/core/blob/5.6.0/registration.php#L28
	 * @used-by \Df\Core\Boot::init()
	 */
	static function check():void {
		try {
			if (error_get_last() && self::isFatal()) {
				self::i()->log();
			}
		}
		catch (\Exception $e) {
			df_log(df_xts($e));
		}
	}

	/** @used-by df_throw_last_error() */
	static function throwLast(:void {
		df_assert(error_get_last());
		df_error(self::i()->main());
	}

	/**
	 * @used-by self::check()
	 * @used-by self::throwLast()
	 */
	private static function i():self {return new self;}

	/**
	 * @used-by self::main()
	 * @used-by self::msg()
	 * @used-by self::type()
	 * @param string $k
	 * @return string|int
	 */
	private static function info($k) {return dfa(error_get_last(), $k);}

	/**
	 * @used-by self::check()
	 * @return bool
	 */
	private static function isFatal() {
		static $r;/** @var array(int => int) $r */
		if (!$r) {
			$r = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING];
			# xDebug при E_RECOVERABLE_ERROR останавивает работу интерпретатора
			if (self::xdebug()) {
				$r[]= E_RECOVERABLE_ERROR;
			}
			$r = array_flip($r);
		}
		return isset($r[self::type()]);
	}

	/**
	 * @used-by self::type()
	 * @return array(int => string)
	 */
	private static function map() {
		static $r; /** @var array(int => string) $r */
		if (!$r) {
			$r = [
				E_COMPILE_ERROR => 'E_COMPILE_ERROR'
				,E_COMPILE_WARNING => 'E_COMPILE_WARNING'
				,E_CORE_ERROR => 'E_CORE_ERROR'
				,E_CORE_WARNING => 'E_CORE_WARNING'
				,E_DEPRECATED => 'E_DEPRECATED'
				,E_ERROR => 'E_ERROR'
				,E_NOTICE => 'E_NOTICE'
				,E_PARSE => 'E_PARSE'
				,E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR'
				,E_STRICT => 'E_STRICT'
				,E_USER_DEPRECATED => 'E_USER_DEPRECATED'
				,E_USER_ERROR => 'E_USER_ERROR'
				,E_USER_NOTICE => 'E_USER_NOTICE'
				,E_USER_WARNING => 'E_USER_WARNING'
				,E_WARNING => 'E_WARNING'
			];
		}
		return $r;
	}

	/**
	 * 2023-01-28 The stack trace returned by @see error_get_last() is chopped.
	 * @used-by self::main()
	 * @return string
	 */
	private static function msg() {return df_path_rel_g(self::info('message'));}

	/**
	 * @used-by self::isFatal()
	 * @used-by self::main()
	 * @param bool $asString [optional]
	 * @return int|string
	 */
	private static function type($asString = false) {
		$r = (int)self::info('type'); /** @var int|string $r */
		return !$asString ? $r : dfa(self::map(), $r);
	}

	/**
	 * @used-by self::isFatal()
	 * @used-by self::trace()
	 * @return bool
	 */
	private static function xdebug() {static $r; return $r ? $r : $r = extension_loaded('xdebug');}
}