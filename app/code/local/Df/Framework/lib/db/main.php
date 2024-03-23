<?php
use Varien_Db_Select as S;

/**
 * 2016-12-01
 * 1) $cols could be:
 * 1.1) a string to fetch a single column;
 * 1.2) an array to fetch multiple columns.
 * @see Zend_Db_Select::_tableCols()
 *		if (!is_array($cols)) {
 *			$cols = array($cols);
 *		}
 * https://github.com/zendframework/zf1/blob/release-1.12.16/library/Zend/Db/Select.php#L929-L931
 * 2) The function always returns @see S
 * I added @see Zend_Db_Select to the PHPDoc return type declaration just for my IDE convenience.
 * @used-by df_customer_att_pos_after()
 * @used-by df_customer_is_new()
 * @used-by df_fetch()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_fetch_one()
 * @used-by df_trans_by_payment()
 * @param string|Entity|array(string => string) $t
 * @param string|string[] $cols [optional]
 * @param string|null $schema [optional]
 * @return S|Zend_Db_Select
 */
function df_db_from($t, $cols = '*', $schema = null):S {return df_select()->from(
	$t instanceof Entity ? $t->getEntityTable() : (is_array($t) ? $t : df_table($t)), $cols, $schema
);}