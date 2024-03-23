<?php
use Mage_Core_Model_Resource as R;

/**
 * 2018-12-07
 * @used-by df_conn()
 */
function df_db_resource():R {return Mage::getSingleton('core/resource');}