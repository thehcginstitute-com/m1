<?php
use Varien_Db_Select as S;
/**
 * 2015-04-13
 * 2024-06-02 "Port `df_fetch_col_int()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/622
 * @used-by df_att_code2id()
 * @used-by df_fetch_col_int_unique()
 * @param string|null|array(string => mixed) $compareK [optional]
 * @param int|string|int[]|string[]|null $compareV [optional]
 * @return int[]|string[]
 */
function df_fetch_col_int(string $t, string $cSelect, $compareK = null, $compareV = null, bool $distinct = false):array {return
	/** I do not use @see df_int() to make the function faster */
	df_int_simple(df_fetch_col($t, $cSelect, $compareK, $compareV, $distinct))
;}

/**
 * 2015-11-03
 * 2024-03-23 "Port `df_fetch_one()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/525
 * @used-by df_fetch_one_int()
 * @used-by hcg_mc_syncd_get()
 * @param string|string[] $cols
 * @param array(string => string) $compare
 * @return string|null|array(string => mixed)
 */
function df_fetch_one(string $t, $cols, array $compare) {
	$s = df_db_from($t, $cols); /** @var S $s */
	foreach ($compare as $c => $v) {/** @var string $c */ /** @var string $v */
		$s->where('? = ' . $c, $v);
	}
	/**
	 * 2016-03-01
	 * @uses \Zend_Db_Adapter_Abstract::fetchOne() возвращает false при пустом результате запроса.
	 * https://mage2.pro/t/853
	 */
	return '*' !== $cols ? df_ftn(df_conn()->fetchOne($s)) : df_eta(df_conn()->fetchRow($s, [], \Zend_Db::FETCH_ASSOC));
}