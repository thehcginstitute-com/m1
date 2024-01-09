<?php
use Exception as E;
/**
 * 2016-10-25 Оказалось, что в ядре нет такой функции.
 * 2022-11-26
 * @see array_search() looks only for a static value (does not support a comparison closure):
 * https://www.php.net/manual/function.array-search.php
 * @used-by df_bt_has()
 * @used-by df_ends_with()
 * @used-by df_handle_prefix()
 * @used-by df_is()
 * @used-by df_modules_my()
 * @used-by df_oq_sa()
 * @used-by df_sales_email_sending()
 * @used-by df_starts_with()
 * @used-by ikf_oi_pid()
 * @used-by mnr_recurring()
 * @used-by \TFC\Core\Plugin\Catalog\Block\Product\View\GalleryOptions::afterGetOptionsJson()
 * @param array|callable|Traversable $a1
 * @param array|callable|Traversable $a2
 * @param mixed|mixed[] $pAppend [optional]
 * @param mixed|mixed[] $pPrepend [optional]
 * @param int $keyPosition [optional]
 * @return mixed|null
 * @throws E
 */
function df_find($a1, $a2, $pAppend = [], $pPrepend = [], $keyPosition = 0) {
	# 2020-03-02, 2022-10-31
	# 1) Symmetric array destructuring requires PHP ≥ 7.1:
	#		[$a, $b] = [1, 2];
	# https://github.com/mage2pro/core/issues/96#issuecomment-593392100
	# We should support PHP 7.0.
	# https://3v4l.org/3O92j
	# https://www.php.net/manual/migration71.new-features.php#migration71.new-features.symmetric-array-destructuring
	# https://stackoverflow.com/a/28233499
	list($a, $f) = dfaf($a1, $a2); /** @var array|Traversable $a */ /** @var callable $f */
	$pAppend = df_array($pAppend); $pPrepend = df_array($pPrepend);
	$r = null; /** @var mixed|null $r */
	foreach ($a as $k => $v) {/** @var int|string $k */ /** @var mixed $v */ /** @var mixed[] $primaryArgument */
		switch ($keyPosition) {
			case DF_BEFORE:
				$primaryArgument = [$k, $v];
				break;
			case DF_AFTER:
				$primaryArgument = [$v, $k];
				break;
			default:
				$primaryArgument = [$v];
		}
		if ($fr = call_user_func_array($f, array_merge($pPrepend, $primaryArgument, $pAppend))) {
			$r = !is_bool($fr) ? $fr : $v;
			break;
		}
	}
	return $r;
}