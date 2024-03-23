<?php
/**
 * @uses Mage_Core_Model_Resource::getTableName() не кэширует результаты своей работы,
 * и, глядя на реализацию @see Mage_Core_Model_Resource_Setup::getTable(),
 * которая выполняет кэширование для @see Mage_Core_Model_Resource::getTableName(),
 * я решил сделать аналогичную функцию, только доступную в произвольном контексте.
 * @param string|string[] $name
 * @return string
 */
function df_table($name) {
	static $cache; /** @var array(string => string) $cache */
	/**
	 * По аналогии с @see Mage_Core_Model_Resource_Setup::_getTableCacheName()
	 * @var string $key
	 */
	$key = is_array($name) ? implode('_', $name) : $name;
	if (!isset($cache[$key])) {
		$cache[$key] = df_db_resource()->getTableName($name);
	}
	return $cache[$key];
}