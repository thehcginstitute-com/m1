<?php
use Mage_Core_Model_Resource as R;
use Varien_Db_Select as S;

/**
 * 2018-12-07
 * @used-by df_conn()
 */
function df_db_resource():R {return Mage::getSingleton('core/resource');}

/**
 * 2015-09-29
 */
function df_select():S {return df_conn()->select();}