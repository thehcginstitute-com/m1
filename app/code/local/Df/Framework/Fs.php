<?php
namespace Df\Framework;
use Throwable as T;
final class Fs {
	/**
	 * @used-by df_mkdir()
	 * @param string $path
	 * @param bool $isDir [optional]
	 * @return void
	 */
	static function createAndMakeWritable($path, $isDir = false) {
		if (file_exists($path)) {
			df_assert(is_dir($path) === $isDir);
			self::chmod($path);
		}
		else {
			$dir = $isDir ? $path : dirname($path); /** @var string $dir */
			if (!file_exists($dir)) {
				self::mkdir($dir);
			}
			else {
				self::chmod($dir);
			}
		}
	}

	/**
	 * @used-by self::createAndMakeWritable()
	 * @param string $path
	 */
	private static function chmod($path) {
		try {
			chmod($path, 0777);
		}
		catch (T $t) {
			/** @var bool $isPermissionDenied */
			$isPermissionDenied = \df_contains($t->getMessage(), 'Permission denied');
			df_error(
				$isPermissionDenied
					? "Операционная система запретила интерпретатору PHP {operation} «{path}»."
					:
					"Не удалась {operation} «{path}»."
					. "\nДиагностическое сообщение интерпретатора PHP: «{message}»."
				, [
					'{operation}' => is_dir($path) ? 'запись в папку' : 'запись файла'
					, '{path}' => $path
					, '{message}' => $t->getMessage()
				]
			);
		}
	}

	/**
	 * used-by self::createAndMakeWritable()
	 * @param string $dir
	 */
	private static function mkdir($dir) {
		try {
			mkdir($dir, 0777, true);
		}
		catch (T $t) {
			/** @var bool $isPermissionDenied */
			$isPermissionDenied = \df_contains($t->getMessage(), 'Permission denied');
			df_error(
				$isPermissionDenied
					? "Операционная система запретила интерпретатору PHP создание папки «{$dir}»."
					: "Не удалось создать папку «{$dir}»."
					. "\nДиагностическое сообщение интерпретатора PHP: «{$t->getMessage()}»."
			);
		}
	}
}