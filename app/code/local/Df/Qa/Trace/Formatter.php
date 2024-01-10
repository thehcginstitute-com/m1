<?php
namespace Df\Qa\Trace;
use Df\Qa\Trace as T;
use Df\Qa\Trace\Frame as F;
# 2020-02-27
# 2024-01-10
# "Port the latest version of `Df\Qa\Trace\Formatter` from `mage2pro/core`":
# https://github.com/thehcginstitute-com/m1/issues/161
final class Formatter {
	/**
	 * 2020-02-27
	 * @used-by df_bt_s()
	 * @used-by \Df\Qa\Failure::postface()
	 */
	static function p(T $t):string {return dfcf(function(T $t):string {return df_try(
		function() use($t) {return df_cc("\n\n", df_map_k($t, function(int $i, F $f):string {$i++; return df_ccc("\n\t", [
			"$i\t" . $f->method()
			# 2023-07-27 "Add GitHub links to backtrace frames": https://github.com/mage2pro/core/issues/285
			,$f->url() ?: df_ccc(':', $f->file(), $f->line())
		]);}));}
		# 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
		,function(\Throwable $th) {
			$r = df_xts($th);
			/**
			 * 2020-02-20
			 * 1) «Function include() does not exist»: https://github.com/tradefurniturecompany/site/issues/60
			 * 2) It is be dangerous to call @see df_log() here, because it will inderectly return us here,
			 * and it could be an infinite loop.
			 */
			static $loop = false;
			if ($loop) {
				df_log_l(__CLASS__, "$r\n{$th->getTraceAsString()}", df_class_l(__CLASS__));
			}
			else {
				$loop = true;
				df_log($th, __CLASS__);
				$loop = false;
			}
		}
	);}, [$t]);}
}