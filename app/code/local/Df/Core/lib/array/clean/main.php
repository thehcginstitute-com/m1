<?php

/**
 * 2015-02-07
 * Обратите внимание,
 * что во многих случаях эффективней использовавать @see array_filter() вместо @see df_clean().
 * https://php.net/manual/function.array-filter.php
 * @see array_filter() с единственным параметром удалит из массива все элементы,
 * чьи значения приводятся к логическому «false».
 * Т.е., помимо наших array('', null, []),
 * @see array_filter() будет удалять из массива также элементы со значениями «false» и «0».
 * Если это соответствует требуемому поведению в конретной точке программного кода,
 * то используйте именно @see array_filter(),
 * потому что встроенная функция @see array_filter() в силу реализации на языке С
 * будет работать на порядки быстрее, нежели @see df_clean().
 * 2015-01-22 Теперь из исходного массива будут удаляться элементы, чьим значением является пустой массив.
 * 2016-11-22
 * К сожалению, короткое решение array_diff($a, array_merge(['', null, []], df_args($remove)))
 * приводит к сбою: «Array to string conversion» в случае многомерности одного из аргументов:
 * http://stackoverflow.com/questions/19830585
 * У нас такая многомерность имеется всегда в связи с ['', null, []].
 * Поэтому вынуждены использовать ручную реализацию.
 * В то же время и предудущая (использованная годами) реализация слишком громоздка:
 * https://github.com/mage2pro/core/blob/1.9.14/Core/lib/array.php?ts=4#L31-L54
 * Современная версия интерпретатора PHP позволяет её сократить.
 * 2017-02-13 Добавил в список удаления «false».
 * @see df_clean_null()
 * @see df_clean_r()
 * @used-by df_cc_class()
 * @used-by df_ccc()
 * @used-by df_clean_xml()
 * @used-by df_context()
 * @used-by df_db_or()
 * @used-by df_fe_name_short()
 * @used-by df_http_get()
 * @used-by df_kv()
 * @used-by df_kv_table()
 * @used-by df_page_result()
 * @used-by df_store_code_from_url()
 * @used-by df_zf_http_last_req()
 * @param mixed ...$k [optional]
 */
function df_clean(array $r, ...$k):array {/** @var mixed[] $r */return df_clean_r(
	$r, array_merge([false], df_args($k)), false
);}

/**
 * 2020-02-05
 * @see df_clean()
 * @see df_clean_null()
 * 1) It works recursively.
 * 2) I does not remove `false`.
 * 2024-03-03 "Port `df_clean_r()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/450
 * @used-by df_clean()
 * @used-by df_clean_r()
 * @used-by \Df\Core\Html\Tag::__construct()
 */
function df_clean_r(array $r, array $k = [], bool $req = true):array {/** @var mixed[] $r */
	/** 2020-02-05 @see array_unique() does not work correctly here, even with the @see SORT_REGULAR flag. */
	$k = array_merge($k, ['', null, []]);
	if ($req) {
		$r = df_map($r, function($v) use($k) {return !is_array($v) ? $v : df_clean_r($v, $k);});
	}
	return df_filter($r, function($v) use($k):bool {return !in_array($v, $k, true);});
}