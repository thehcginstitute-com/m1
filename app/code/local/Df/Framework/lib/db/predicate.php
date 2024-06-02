<?php
/**
 * 2015-04-13
 * 2024-06-02 "Port `df_sql_predicate_simple()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/625
 * @used-by df_fetch()
 * @used-by df_fetch_col()
 * @used-by df_fetch_col_max()
 * @used-by df_table_delete()
 * @param int|string|int[]|string[] $v
 */
function df_sql_predicate_simple($v, bool $not = false):string {return
	is_array($v) ? ($not ? 'NOT IN (?)' : 'IN (?)') : ($not ? '<> ?' : '= ?')
;}