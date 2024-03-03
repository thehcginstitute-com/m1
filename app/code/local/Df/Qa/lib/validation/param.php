<?php
use Df\Core\Exception as DFE;
use Df\Qa\Method as Q;
/**
 * 2024-03-03 "Port `df_param_sne()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/451
 * @used-by df_assert_class_exists()
 * @used-by df_is_abstract()
 * @used-by df_date_from_db()
 * @used-by df_eav_update()
 * @used-by df_fs_delete()
 * @used-by df_xml_children()
 * @used-by df_xml_parse()
 * @used-by dfa_deep()
 * @used-by dfa_deep_unset()
 * @used-by \Df\Core\Html\Tag::openTagWithAttributesAsText()
 * @throws DFE
 */
function df_param_sne(string $v, int $ord, int $sl = 0):string {$sl++;
	/**
	 * Раньше тут стояло `$method->assertParamIsString($v, $ord, $sl)`
	 * При второй попытке тут стояло `if (!$v)`, что тоже неправильно, ибо непустая строка '0' не проходит такую валидацию.
	 * 2022-11-10 @see df_result_sne()
	 */
	return !df_es($v) ? $v : Q::raiseErrorParam(__FUNCTION__, [Q::NES], $ord, $sl);
}