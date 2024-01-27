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
	 * @used-by self::cfgH()
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
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountMessageByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountSuccessMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountTemplateByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getAccountUrlByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLoginRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLoginUrlByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLogoutRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getLogoutUrlByGroup()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getNewsletterRedirectionUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getRedirectToParamUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::isAccountGroupTemplateEmpty()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::log()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::switchCustomerLogoutTemplateIf()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::unsetCustomerLogoutChildIf()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Customer::controllerActionPostdispatchCustomerAccountLogout()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::getAccessibleLandingPage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::getAccountActivationDefaultStatus()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::getNonActivatedLandingPage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::getRestrictedLandingPage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::hasRestrictedPaymentMethods()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::hasRestrictedShippingMethods()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccessibleCategoryPageRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccessibleCheckoutPageRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccessibleCmsPageRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccessibleModulePageRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccessibleProductPageRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccountRegistrationDisabled()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAddToCartSectionRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isCustomerGroupAllowedForRestrictedArea()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isCustomerGroupAllowedForRestrictedStore()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isNewAccountActivationEnabled()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isPaymentMethodSectionRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isPriceSectionRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isRestrictedCategoryPageAccessible()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isRestrictedCmsPageAccessible()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isRestrictedModulePageAccessible()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isRestrictedProductPageAccessible()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isRestrictionTypeAccessibleRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isShippingMethodSectionRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::log()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::sendAdminNotificationEmail()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::sendCustomerDeActivationNotificationEmail()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::sendCustomerNotificationEmail()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::switchAccountLoginTemplateIf()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::switchCheckoutLoginTemplateIf()
	 * @used-by \MagePsycho_Storerestrictionpro_Model_Observer::controllerActionPredispatch()
	 * @used-by \MagePsycho_Storerestrictionpro_Model_Observer::customerLogin()
	 * @used-by app/design/frontend/base/default/template/magepsycho/customerregfields/customer/widget/type/group_code.phtml
	 * @used-by app/design/frontend/base/default/template/magepsycho/customerregfields/customer/widget/type/group_id.phtml
	 * @used-by app/design/frontend/default/mobileshoppe/template/magepsycho/storerestrictionpro/checkout/onepage/login.phtml
	 * @used-by app/design/frontend/default/mobileshoppe/template/magepsycho/storerestrictionpro/customer/form/login.phtml
	 * @used-by app/design/frontend/default/mobileshoppe/template/magepsycho/storerestrictionpro/product/view/addtocart.phtml
	 * @used-by app/design/frontend/default/mobileshoppe/template/magepsycho/storerestrictionpro/product/view/price.phtml
	 * @used-by app/design/frontend/default/mobileshoppe/template/magepsycho/storerestrictionpro/product/view/scripts.phtml
	 * @return CfgC|CfgL|CfgS
	 */
	final function cfgH() {return \Mage::helper("{$this->moduleMf()}/config");}

	/**
	 * 2024-01-27
	 * @used-by \MagePsycho_Customerregfields_Block_Customer_Widget_Type::_toHtml()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::switchCustomerFormRegisterTemplateIf()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::switchCustomerAccountEditTemplateIf()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::switchCheckoutOnepageBillingTemplateIf()
	 * @used-by \MagePsycho_Customerregfields_Model_Observer::controllerActionPostdispatchCheckoutOnepageSaveBilling()
	 * @used-by \MagePsycho_Customerregfields_Model_Observer::checkoutTypeOnepageSaveOrder()
	 * @used-by \MagePsycho_Customerregfields_Model_Observer::salesOrderSaveAfter()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::unsetCustomerLogoutChildIf()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::switchCustomerLogoutTemplateIf()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer::controllerActionPredispatch()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Customer::customerRegisterSuccess()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Customer::controllerActionPostdispatchCustomerAccountCreatePost()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Customer::customerLogin()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Customer::controllerActionPostdispatchCustomerAccountLogout()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Customer::controllerFrontInitBefore()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer_Newsletter::controllerActionPostdispatchNewsletterSubscriber()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::skipRestrictionByDefault()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::switchAccountLoginTemplateIf()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::switchCheckoutLoginTemplateIf()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::skipAccountActivationFxn()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isPriceSectionRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAddToCartSectionRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isAccessibleCheckoutPageRestricted()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::skipPaymentMethodRestriction()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::skipShippingMethodRestriction()
	 */
	final function isFxnSkipped():bool {return !$this->isActive();}

	/**
	 * 2024-01-27
	 * @used-by self::__construct()
	 * @used-by self::isActive()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::getAllowedCustomerGroups()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::getGroupCodeData()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::getGroupCodeErrorMessage()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::getGroupSelectionLabel()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::getGroupSelectionType()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::isEnabledForCheckout()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::isGroupFieldRequired()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::isGroupSelectionEditable()
	 * @used-by \MagePsycho_Customerregfields_Helper_Config::isLogEnabled()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getDefaultAccountMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getDefaultAccountUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getDefaultLoginUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getDefaultLogoutUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getGroupAccountMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getGroupAccountTemplate()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getGroupAccountUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getGroupLoginUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getGroupLogoutUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getLogoutDelay()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getLogoutMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getNewsletterUrl()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getRedirectToParam()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::getRemoveLogoutIntermediate()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Config::isLogEnabled()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleAllowedCustomerGroups()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideAddToCart()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideAddToCartLink()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideAddToCartText()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideCheckout()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideProductPriceLink()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideProductPriceText()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleHideProductPrices()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRedirectionType()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRedirectionTypeCms()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRedirectionTypeCustom()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRestrictedCategoryPages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRestrictedCmsPages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRestrictedModulePages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRestrictedPaymentMethods()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRestrictedProductPages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleRestrictedShipmentMethods()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAccessibleStoreErrorMessage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getActivationRequiredCustomerGroups()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getAdminNotificationEmailTemplate()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getCustomerDeActivationNotificationEmailTemplate()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getCustomerNotificationEmailTemplate()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getCustomerRegistrationNotificationAdminEmails()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getDomainType()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAcccountRegistrationDisabledMessage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationByDefaultFrontend()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationRedirectionErrorMessageLogin()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationRedirectionErrorMessageRegistration()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationRedirectionType()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationRedirectionTypeCms()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationRedirectionTypeCustom()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountActivationRequired()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNewAccountRegistrationOption()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNotifyAdminOnCustomerRegistration()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNotifyCustomerOnAccountActivation()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getNotifyCustomerOnAccountDeActivation()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedAllowedCategoryPages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedAllowedCmsPages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedAllowedCustomerGroups()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedAllowedModulePages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedAllowedProductPages()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedCustomerGroupErrorMessage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedRedirectionType()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedRedirectionTypeCms()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedRedirectionTypeCustom()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictedStoreErrorMessage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::getRestrictionType()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Config::isLogEnabled()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::_getEmails()
	 */
    final protected function cfg(string $p, $s = null) {return \Mage::getStoreConfig("{$this->moduleMf()}/$p", $s);}

	/**
	 * 2024-01-27
	 * @used-by self::isFxnSkipped()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::log()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::skipGroupCodeSelectorFxn()
	 */
    final protected function isActive($storeId = null):bool {return (bool)$this->cfg('option/active', $storeId);}
}