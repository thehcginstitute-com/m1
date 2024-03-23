<?php
use Mage_Core_Model_Resource as R;

/**
 * 2018-12-07
 * @used-by df_conn()
 */
function df_db_resource():R {return Mage::getSingleton('core/resource');}

/**
 * 2015-09-29
 * @return Varien_Db_Select
 */
function df_select() {return df_conn()->select();}