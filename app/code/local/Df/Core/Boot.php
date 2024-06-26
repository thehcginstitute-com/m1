<?php
namespace Df\Core;
use Varien_Event_Observer as O;
# # 2023-07-10 https://github.com/magento-russia/3/blob/2023-07-10/app/code/local/Df/Core/Boot.php
class Boot {
	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 */
	function _default() {self::run();}

	/**
	 * 2015-08-03
	 * При установке РСМ одновременно с CE controller_front_init_before — это первое событие,
	 * которое становится доступно подписчикам.
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 */
	function controller_front_init_before() {self::run();}

	/**
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 */
	function resource_get_tablename(O $o):void {
		if (!self::$_done) {
			$t = $o['table_name']; /** @var string $t; */
			/**
			 * 1) Мы бы рады инициализировать нашу библиотеку при загрузке таблицы «core_resource»,
			 * однако в тот момент система оповещений о событиях ещё не работает, и мы сюда всё равно не попадём.
			 * 2) Проблема инициализации Российской сборки Magento при работе стороронних установочных скриптов
			 * удовлетворительно решается методом @see Df_Core_Helper_DataM::useDbCompatibleMode()
			 */
			if ('core_website' === $t || 'index_process' === $t && self::isCompilationFromCommandLine()) {
				self::run();
			}
		}
	}

	/**
	 * 2015-03-06
	 * Вероятно, часть этих вызовов уже избыточны,
	 * потому что инициализация за последние годы развития Российской сборки Magento
	 * стала более «умной»: @see self::resource_get_tablename()
	 * @used-by self::_default()
	 * @used-by self::controller_front_init_before()
	 * @used-by self::resource_get_tablename()
	 */
	static function run() {
		if (!self::$_done) {
			self::init();
			self::$_done = true;
		}
	}

	/**
	 * Этот метод содержит код инициализации, который должен выполняться как можно раньше:
	 * вне зависимости, был ли уже инициализирован текущий магазин системы.
	 * Соответственно, в этом методе мы не можем работать с объектами-магазинами.
	 * В том числе, в частости, не можем прочитывать настройки текущего магазина.
	 * @used-by self::run()
	 */
	private static function init():void {
		self::lib();
		register_shutdown_function(function():void {\Df\Qa\Failure\Error::check();});
	}

	/** 
	 * @used-by self::resource_get_tablename()
	 */
	private static function isCompilationFromCommandLine():bool {static $r; return $r ?: $r =
		# 2024-01-10 I removed `@` by analogy with https://github.com/thehcginstitute-com/m1/commit/6815ccd0
		class_exists('Mage_Shell_Compiler', false)
	;}

	/**
	 * 2023-07-10 https://github.com/itsapiece/all/blob/2023-07-10/app/code/local/ItsAPiece/PinkTown/Importer.php#L159-L183
	 * @used-by self::init()
	 */
	private static function lib() {
		# 2017-11-13
		# Today I have added the subdirectories support inside the `lib` folders,
		# because some lib/*.php files became too big, and I want to split them.
		$requireFiles = function(string $libDir) use(&$requireFiles):void {
			# 2015-02-06 array_slice removes «.» and «..»: https://php.net/manual/function.scandir.php#107215
			foreach (array_slice(scandir($libDir), 2) as $c) {  /** @var string $resource */
				is_dir($resource = "{$libDir}/{$c}") ? $requireFiles($resource) : require_once "{$libDir}/{$c}";
			}
		};
		$iterate = function(string $base, array $mm) use($requireFiles):void  {
			foreach ($mm as $m) {/** @var string $m */
				# 2016-11-23 It gets rid of the ['..', '.'] and the root files (non-directories).
				if (ctype_upper($m[0]) && is_dir($baseM = $base . $m)) {/** @var string $baseM */
					if (is_dir($libDir = "{$baseM}/lib")) {/** @var string $libDir */
						$requireFiles($libDir);
					}
				}
			}
		};
		# 2017-06-18 The strange array_diff / array_merge combination makes the Df_Core module to be loaded first.
		/** @var string $p */
		$iterate($p = dirname(dirname(__FILE__)) . '/', array_merge(['Core'], array_diff(scandir($p), ['Core'])));
		# 2024-01-28
		# "Support the `lib` functions autoloading for the `HCG_*` modules":
		# https://github.com/thehcginstitute-com/m1/issues/334
		$iterate($p = dirname(dirname(dirname(__FILE__))) . '/HCG/', scandir($p));
	}

	/**
	 * @used-by self::resource_get_tablename()
	 * @used-by self::run()
	 * @var bool
	 */
	private static $_done = false;
}