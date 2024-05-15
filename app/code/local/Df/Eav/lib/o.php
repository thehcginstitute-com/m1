<?php
use Mage_Eav_Model_Config as Config;
/**
 * 2015-10-12
 * 2024-05-15 "Port `df_eav_config()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/610
 * @used-by df_eav_ca()
 * @used-by df_eav_customer()
 */
function df_eav_config():Config {return Mage::getSingleton('eav/config');}