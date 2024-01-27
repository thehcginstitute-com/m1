<?php
namespace HCG\MagePsycho;
use \MagePsycho_Customerregfields_Helper_Config as CfgC;
use \MagePsycho_Loginredirectpro_Helper_Config as CfgL;
use \MagePsycho_Storerestrictionpro_Helper_Config as CfgS;
/**
 * 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
 * @see \MagePsycho_Customerregfields_Helper_Data
 * @see \MagePsycho_Loginredirectpro_Helper_Data
 * @see \MagePsycho_Storerestrictionpro_Helper_Data
 */
abstract class Helper extends \Mage_Core_Helper_Abstract {
	/**
	 * 2024-01-27
	 * @used-by self::cfg()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::log()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::log()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::log()
	 * @see \MagePsycho_Customerregfields_Helper_Data::moduleMf()
	 * @see \MagePsycho_Loginredirectpro_Helper_Data::moduleMf()
	 * @see \MagePsycho_Storerestrictionpro_Helper_Data::moduleMf()
	 */
	abstract protected function moduleMf():string;

	/**
	 * 2024-01-27
	 * @used-by self::isActive()
	 * @used-by \MagePsycho_Customerregfields_Block_Customer_Widget_Abstract::getConfig()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::_getDbGroupCodes()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::getGroupSelectOptions()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::isValidCustomerForEdit()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::log()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::skipGroupCodeSelectorFxn()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::switchCheckoutOnepageBillingTemplateIf()
	 * @used-by \MagePsycho_Customerregfields_Model_Customer_Attribute_Data_Groupcode::validateValue()
	 * @used-by \MagePsycho_Loginredirectpro_Block_Customer_Logout::getCustomMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Block_Customer_Logout::getDelayTime()
	 * @used-by \MagePsycho_Loginredirectpro_Block_Customer_Logout::getRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::log()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getRedirectToParamUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLoginUrlByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLogoutUrlByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountUrlByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountMessageByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountTemplateByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLoginRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLogoutRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountSuccessMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::isAccountGroupTemplateEmpty()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getNewsletterRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::unsetCustomerLogoutChildIf()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::switchCustomerLogoutTemplateIf()
	 * @return CfgC|CfgL|CfgS
	 */
	final function cfgH() {return \Mage::helper('magepsycho_customerregfields/config');}

	/**
	 * 2024-01-27
	 */
	final function isFxnSkipped():bool {return !$this->isActive();}

	/**
	 * 2024-01-27
	 * @used-by self::__construct()
	 */
    final protected function cfg(string $xmlPath, $storeId = null) {return \Mage::getStoreConfig(
		$this->moduleMf() . '/' . $xmlPath, $storeId
	);}

	/**
	 * 2024-01-27
	 * @used-by self::isFxnSkipped()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::log()
	 */
    final protected function isActive():bool {return (bool)$this->cfgH()->isActive();}
}