<?php
use Mage_Eav_Model_Entity_Type as T;

/**
 * 2015-10-12
 * 2024-05-15 "Port `df_eav_customer()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/611
 * @used-by df_customer_att()
 */
function df_eav_customer():T {return df_eav_config()->getEntityType('customer');}