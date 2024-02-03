<?php
use MagePsycho_Customerregfields_Helper_Data as hC;
use MagePsycho_Storerestrictionpro_Helper_Data as hS;
/**
 * 2024-01-28 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
 * @used-by \MagePsycho_Customerregfields_Block_Customer_Widget_Abstract::getConfig()
 * @used-by \MagePsycho_Customerregfields_Block_Customer_Widget_Type::_toHtml()
 * @used-by \MagePsycho_Customerregfields_Block_Customer_Widget_Type_Groupid::getGroupSelectOptions()
 * @used-by \MagePsycho_Customerregfields_Block_System_Config_Domain::_getElementHtml()
 * @used-by \MagePsycho_Customerregfields_Block_System_Config_Groupcode::_prepareToRender()
 * @used-by \MagePsycho_Customerregfields_Block_System_Config_Version::_getElementHtml()
 * @used-by \MagePsycho_Customerregfields_Model_Customer_Attribute_Data_Groupcode::validateValue()
 * @used-by \MagePsycho_Customerregfields_Model_Observer::adminhtmlInitSystemConfig()
 * @used-by \MagePsycho_Customerregfields_Model_Observer::checkoutTypeOnepageSaveOrder()
 * @used-by \MagePsycho_Customerregfields_Model_Observer::controllerActionPostdispatchCheckoutOnepageSaveBilling()
 * @used-by \MagePsycho_Customerregfields_Model_Observer::customerSaveBefore()
 * @used-by \MagePsycho_Customerregfields_Model_Observer::salesOrderSaveAfter()
 * @used-by \MagePsycho_Customerregfields_Model_System_Config_Source_Customergroups::toOptionArray()
 * @used-by \MagePsycho_Customerregfields_Model_System_Config_Source_Domaintypes::getAllOptions()
 * @used-by \MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::getAllOptions()
 * @used-by app/code/local/MagePsycho/Customerregfields/sql/magepsycho_customerregfields_setup/mysql4-install-0.1.0.php
 * @used-by app/code/local/MagePsycho/Customerregfields/sql/magepsycho_customerregfields_setup/mysql4-upgrade-0.1.0-0.2.2.php
 * @used-by app/design/frontend/base/default/template/checkout/onepage/billing.phtml
 * @used-by app/design/frontend/base/default/template/customer/form/edit.phtml
 * @used-by app/design/frontend/base/default/template/customfield/customfields.phtml
 * @used-by app/design/frontend/base/default/template/magepsycho/customerregfields/customer/widget/type/group_code.phtml
 * @used-by app/design/frontend/base/default/template/magepsycho/customerregfields/customer/widget/type/group_id.phtml
 */
function hcg_mp_hc():hC {return \Mage::helper('magepsycho_customerregfields');}

/**
 * 2024-02-03 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
 * @unused
 */
function hcg_mp_hs():hS {return \Mage::helper('magepsycho_storerestrictionpro');}