<?php
namespace Df\Qa;
use Df\Qa\Trace\Frame;
use Exception as E;
use ReflectionParameter as RP;
use Zend_Validate_Interface as Vd;
final class Method {
	/**
	 * @used-by df_param_sne()
	 * @used-by self::vp()
	 * @param string $method
	 * @param int $ord
	 * @param int $sl
	 * @throws E
	 */
	static function raiseErrorParam($method, array $messages, $ord, $sl = 1) {
		$frame = self::caller($sl); /** @var Frame $frame */
		$name = 'unknown'; /** @var string $name */
		if ($frame->methodR()) {/** @var RP $param */
			$name = $frame->methodParameter($ord)->getName();
		}
		$messagesS = df_cc_n($messages); /** @var string $messagesS */
		self::throwException(
			"[{$frame->method()}]"
			."\nThe argument «{$name}» is rejected by the «{$method}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			,$sl
		);
	}

	/**
	 * @used-by df_result_s()
	 * @used-by df_result_sne()
	 * @param string $vd
	 * @param int $sl
	 * @throws E
	 */
	static function raiseErrorResult($vd, array $messages, $sl = 1) {
		$messagesS = df_cc_n($messages); /** @var string $messagesS */
		$method = self::caller($sl)->method(); /** @var string $method */
		self::throwException(
			"[{$method}]\nA result of this method is rejected by the «{$vd}» validator."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			, $sl
		);
	}

	/**
	 * @used-by df_assert_sne()
	 * @param string $vd
	 * @param int $sl
	 * @throws E
	 */
	static function raiseErrorVariable($vd, array $messages, $sl = 1) {
		$messagesS = df_cc_n($messages); /** @var string $messagesS */
		$method = self::caller($sl)->method(); /** @var string $method */
		self::throwException(
			"[{$method}]\nThe validator «{$vd}» has catched a variable with an invalid value."
			."\nThe diagnostic message:\n{$messagesS}\n\n"
			,$sl
		);
	}

	/**
	 * 2017-01-12
	 * @used-by df_assert_sne()
	 * @used-by df_param_sne()
	 * @used-by df_result_sne()
	 */
	const NES = 'A non-empty string is required, but got an empty one.';

	/**
	 * Объект @see Frame конструируется на основе $o + 2,
	 * потому что нам нужно вернуть название метода, который вызвал тот метод, который вызвал метод caller.
	 * @used-by self::raiseErrorParam()
	 * @used-by self::raiseErrorResult()
	 * @used-by self::raiseErrorVariable()
	 * @param int $o
	 * @return Frame
	 */
	private static function caller($o) {return Frame::i(df_bt(0, 3 + $o)[2 + $o]);}

	/**
	 * 2015-01-28
	 * Раньше тут стояло throw $e, что приводило к отображению на экране диагностического сообщения в неверной кодировке.
	 * @uses df_error() точнее: эта функция в режиме разработчика отсылает браузеру заголовок HTTP о требуемой кодировке.
	 * @used-by self::raiseErrorParam()
	 * @used-by self::raiseErrorResult()
	 * @used-by self::raiseErrorVariable()
	 * @param string $message
	 * @param int $sl [optional]
	 * @throws E
	 */
	private static function throwException($message, $sl = 0) {df_error(new E($message, ++$sl));}
	
	/**
	 * @used-by self::assertParamIsIso2()
	 * @param mixed $v
	 * @param int $ord
	 * @param int $sl [optional]
	 * @return mixed
	 * @throws E
	 */
	private static function vp(Vd $vd, $v, $ord, $sl = 1) {return $vd->isValid($v) ? $v : self::raiseErrorParam(
		get_class($vd), $vd->getMessages(), $ord, ++$sl
	);}
}