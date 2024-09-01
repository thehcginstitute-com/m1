<?php
namespace Df\Framework;
final class Fs {
	/**
	 * @used-by df_mkdir()
	 * @used-by df_file_write()
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
			$r = chmod($path, 0777);
			df_throw_last_error($r);
		}
		catch (\Exception $e) {
			/** @var bool $isPermissionDenied */
			$isPermissionDenied = \df_contains($e->getMessage(), 'Permission denied');
			df_error(
				$isPermissionDenied
					? "Операционная система запретила интерпретатору PHP {operation} «{path}»."
					:
					"Не удалась {operation} «{path}»."
					. "\nДиагностическое сообщение интерпретатора PHP: «{message}»."
				, [
					'{operation}' => is_dir($path) ? 'запись в папку' : 'запись файла'
					, '{path}' => $path
					, '{message}' => $e->getMessage()
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
			$r = mkdir($dir, 0777, $recursive = true);
			df_throw_last_error($r);
		}
		catch (\Exception $e) {
			/** @var bool $isPermissionDenied */
			$isPermissionDenied = \df_contains($e->getMessage(), 'Permission denied');
			df_error(
				$isPermissionDenied
					? "Операционная система запретила интерпретатору PHP создание папки «{$dir}»."
					: "Не удалось создать папку «{$dir}»."
					. "\nДиагностическое сообщение интерпретатора PHP: «{$e->getMessage()}»."
			);
		}
	}
}