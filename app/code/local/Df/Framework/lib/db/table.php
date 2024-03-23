<?php
/**
 * @uses Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию @see Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @used-by df_db_column_add()
 * @used-by df_db_column_describe()
 * @used-by df_db_column_drop()
 * @used-by df_db_column_exists()
 * @used-by df_db_column_rename()
 * @used-by df_db_drop_pk()
 * @used-by df_db_from()
 * @used-by df_next_increment()
 * @used-by df_next_increment_set()
 * @used-by df_table_delete()
 * @used-by df_table_exists()
 * @param string|string[] $n
 */
function df_table($n):string {return dfcf(function($n) {return df_db_resource()->getTableName($n);}, [$n]);}