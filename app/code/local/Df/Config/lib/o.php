<?php
use Mage_Core_Model_Resource_Config_Data_Collection as C;
/**
 * 2024-06-08
 * "Implement `df_config_c()`": https://github.com/thehcginstitute-com/m1/issues/640
 * @used-by Ebizmarts_MailChimp_Helper_Data::getIfConfigExistsForScope()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getScopeByMailChimpStoreId()
 */
function df_config_c():C {return Mage::getResourceModel('core/config_data_collection');}