<?php
use Varien_Db_Adapter_Pdo_Mysql as Mysql;
use Varien_Db_Adapter_Interface as IAdapter;

/**
 * 2018-12-07
 * @return IAdapter|Mysql
 */
function df_conn():IAdapter {static $r; return $r ? $r : $r = df_db_resource()->getConnection('write');}