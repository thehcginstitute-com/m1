<?php
use Mage_Eav_Model_Entity_Attribute_Abstract as A;
/**
 * 2016-06-04
 * 2024-05-15 "Port `df_customer_att()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/609
 * @used-by df_customer_att_is_required()
 */
function df_customer_att(string $c):A {return df_eav_config()->getAttribute(df_eav_customer(), $c);}