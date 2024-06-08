<?php
use Mage_Core_Model_Resource_Config_Data_Collection as C;
/**
 * 2024-06-08
 * "Implenent `df_config_c()`": https://github.com/thehcginstitute-com/m1/issues/640
 */
function df_config_c():C {return Mage::getResourceModel('core/config_data_collection');}