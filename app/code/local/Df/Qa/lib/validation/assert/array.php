<?php
use Df\Core\Exception as DFE;
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2017-01-14 Отныне функция возвращает $v: это позволяет нам значительно сократить код вызова функции.
 * 2024-05-16 "Port `df_assert_in()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/613
 * @used-by df_assert_address_type()
 * @used-by df_date_from_timestamp_14()
 * @param string|float|int|bool|null $v
 * @param array(string|float|int|bool|null) $a
 * @param string|T $m [optional]
 * @return string|float|int|bool|null
 * @throws DFE
 */
function df_assert_in($v, array $a, $m = null) {
	if (!in_array($v, $a, true)) {
		df_error($m ?: "The value «{$v}» is rejected" . (
			10 >= count($a)
				? sprintf(". Allowed values: «%s».", df_csv_pretty($a))
				: " because it is absent in the list of allowed values."
		));
	}
	return $v;
}