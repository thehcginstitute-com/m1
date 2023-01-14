<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Helper_Config extends MagePsycho_Storerestrictionpro_Helper_Data
{
    /* General */
    const XML_PATH_ACTIVE       = 'option/active';
    const XML_PATH_ENABLE_LOG   = 'option/enable_log';
    const XML_PATH_DOMAIN_TYPE  = 'option/domain_type';

    /* Registration/Activation */
    const XML_PATH_NEW_ACCCOUNT_REGISTRATION_OPTION = 'new_account_settings/new_acccount_registration_option';
    const XML_PATH_NEW_ACCCOUNT_REGISTRATION_DISABLED_MESSAGE = 'new_account_settings/new_acccount_registration_disabled_message';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REQUIRED = 'new_account_settings/new_account_activation_required';
    const XML_PATH_ACTIVATION_REQUIRED_CUSTOMER_GROUPS = 'new_account_settings/activation_required_customer_groups';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_BY_DEFAULT_FRONTEND = 'new_account_settings/new_account_activation_by_default_frontend';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_BY_DEFAULT_ADMIN = 'new_account_settings/new_account_activation_by_default_admin';

    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_TYPE = 'new_account_settings/new_account_activation_redirection_type';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_TYPE_CMS = 'new_account_settings/new_account_activation_redirection_type_cms';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_TYPE_CUSTOM = 'new_account_settings/new_account_activation_redirection_type_custom';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_ERROR_MESSAGE_REGISTRATION = 'new_account_settings/new_account_activation_redirection_error_message_registration';
    const XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_ERROR_MESSAGE_LOGIN = 'new_account_settings/new_account_activation_redirection_error_message_login';
    const XML_PATH_NOTIFY_ADMIN_ON_CUSTOMER_REGISTRATION = 'new_account_settings/notify_admin_on_customer_registration';
    const XML_PATH_CUSTOMER_REGISTRATION_NOTIFICATION_ADMIN_EMAILS = 'new_account_settings/customer_registration_notification_admin_emails';
    const XML_PATH_NOTIFY_CUSTOMER_ON_ACCOUNT_ACTIVATION = 'new_account_settings/notify_customer_on_account_activation';
    const XML_PATH_ADMIN_NOTIFICATION_EMAIL_TEMPLATE = 'new_account_settings/admin_notification_email_template';
    const XML_PATH_CUSTOMER_NOTIFICATION_EMAIL_TEMPLATE = 'new_account_settings/customer_notification_email_template';
    const XML_PATH_CUSTOMER_DEACTIVATION_NOTIFICATION_EMAIL_TEMPLATE = 'new_account_settings/customer_deactivation_notification_email_template';

    /* Restricted / Accessible */
    const XML_PATH_RESTRICTION_TYPE = 'restricted_settings/restriction_type';
    const XML_PATH_RESTRICTED_ALLOWED_CUSTOMER_GROUPS = 'restricted_settings/restricted_allowed_customer_groups';
    const XML_PATH_RESTRICTED_REDIRECTION_TYPE = 'restricted_settings/restricted_redirection_type';
    const XML_PATH_RESTRICTED_REDIRECTION_TYPE_CMS = 'restricted_settings/restricted_redirection_type_cms';
    const XML_PATH_RESTRICTED_REDIRECTION_TYPE_CUSTOM = 'restricted_settings/restricted_redirection_type_custom';
    const XML_PATH_RESTRICTED_STORE_ERROR_MESSAGE = 'restricted_settings/restricted_store_error_message';
    const XML_PATH_RESTRICTED_CUSTOMER_GROUP_ERROR_MESSAGE = 'restricted_settings/restricted_customer_group_error_message';
    const XML_PATH_RESTRICTED_ALLOWED_CMS_PAGES = 'restricted_settings/restricted_allowed_cms_pages';
    const XML_PATH_RESTRICTED_ALLOWED_CATEGORY_PAGES = 'restricted_settings/restricted_allowed_category_pages';
    const XML_PATH_RESTRICTED_ALLOWED_PRODUCT_PAGES = 'restricted_settings/restricted_allowed_product_pages';
    const XML_PATH_RESTRICTED_ALLOWED_MODULE_PAGES = 'restricted_settings/restricted_allowed_module_pages';

    /* Accessible / Restricted */
    const XML_PATH_ACCESSIBLE_ALLOWED_CUSTOMER_GROUPS = 'restricted_settings/accessible_allowed_customer_groups';
    const XML_PATH_ACCESSIBLE_REDIRECTION_TYPE = 'restricted_settings/accessible_redirection_type';
    const XML_PATH_ACCESSIBLE_REDIRECTION_TYPE_CMS = 'restricted_settings/accessible_redirection_type_cms';
    const XML_PATH_ACCESSIBLE_REDIRECTION_TYPE_CUSTOM = 'restricted_settings/accessible_redirection_type_custom';
    const XML_PATH_ACCESSIBLE_STORE_ERROR_MESSAGE = 'restricted_settings/accessible_store_error_message';
    const XML_PATH_ACCESSIBLE_HIDE_PRODUCT_PRICES = 'restricted_settings/accessible_hide_product_prices';
    const XML_PATH_ACCESSIBLE_HIDE_PRODUCT_PRICE_TEXT = 'restricted_settings/accessible_hide_product_price_text';
    const XML_PATH_ACCESSIBLE_HIDE_PRODUCT_PRICE_LINK = 'restricted_settings/accessible_hide_product_price_link';
    const XML_PATH_ACCESSIBLE_HIDE_ADD_TO_CART = 'restricted_settings/accessible_hide_add_to_cart';
    const XML_PATH_ACCESSIBLE_HIDE_ADD_TO_CART_TEXT = 'restricted_settings/accessible_hide_add_to_cart_text';
    const XML_PATH_ACCESSIBLE_HIDE_ADD_TO_CART_LINK = 'restricted_settings/accessible_hide_add_to_cart_link';
    const XML_PATH_ACCESSIBLE_HIDE_CHECKOUT = 'restricted_settings/accessible_hide_checkout';
    const XML_PATH_ACCESSIBLE_RESTRICTED_SHIPMENT_METHODS = 'restricted_settings/accessible_restricted_shipment_methods';
    const XML_PATH_ACCESSIBLE_RESTRICTED_PAYMENT_METHODS = 'restricted_settings/accessible_restricted_payment_methods';

    const XML_PATH_ACCESSIBLE_RESTRICTED_CMS_PAGES = 'restricted_settings/accessible_restricted_cms_pages';
    const XML_PATH_ACCESSIBLE_RESTRICTED_CATEGORY_PAGES = 'restricted_settings/accessible_restricted_category_pages';
    const XML_PATH_ACCESSIBLE_RESTRICTED_PRODUCT_PAGES = 'restricted_settings/accessible_restricted_product_pages';
    const XML_PATH_ACCESSIBLE_RESTRICTED_MODULE_PAGES = 'restricted_settings/accessible_restricted_module_pages';

    /****************************************************************************************
     * GENERIC
     *****************************************************************************************/
    public function isActive($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACTIVE, $storeId);
    }

    public function isLogEnabled($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ENABLE_LOG, $storeId);
    }

    public function getDomainType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DOMAIN_TYPE, $storeId);
    }

    /****************************************************************************************
     * REGISTRATION / ACTIVATION
     *****************************************************************************************/
    public function getNewAccountRegistrationOption($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCCOUNT_REGISTRATION_OPTION, $storeId);
    }

    public function getNewAcccountRegistrationDisabledMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCCOUNT_REGISTRATION_DISABLED_MESSAGE, $storeId);
    }

    public function getNewAccountActivationRequired($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_REQUIRED, $storeId);
    }

    public function getNewAccountActivationByDefaultFrontend($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_BY_DEFAULT_FRONTEND, $storeId);
    }

    public function getActivationRequiredCustomerGroups($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACTIVATION_REQUIRED_CUSTOMER_GROUPS, $storeId);
    }

    public function getNotifyAdminOnCustomerRegistration($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NOTIFY_ADMIN_ON_CUSTOMER_REGISTRATION, $storeId);
    }

    public function getCustomerRegistrationNotificationAdminEmails($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CUSTOMER_REGISTRATION_NOTIFICATION_ADMIN_EMAILS, $storeId);
    }

    public function getNotifyCustomerOnAccountActivation($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NOTIFY_CUSTOMER_ON_ACCOUNT_ACTIVATION, $storeId);
    }

    public function getNewAccountActivationRedirectionErrorMessageRegistration($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_ERROR_MESSAGE_REGISTRATION, $storeId);
    }

    public function getNewAccountActivationRedirectionErrorMessageLogin($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_ERROR_MESSAGE_LOGIN, $storeId);
    }

    public function getNewAccountActivationRedirectionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_TYPE, $storeId);
    }

    public function getNewAccountActivationRedirectionTypeCms($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_TYPE_CMS, $storeId);
    }

    public function getNewAccountActivationRedirectionTypeCustom($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEW_ACCOUNT_ACTIVATION_REDIRECTION_TYPE_CUSTOM, $storeId);
    }

    public function getAdminNotificationEmailTemplate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ADMIN_NOTIFICATION_EMAIL_TEMPLATE, $storeId);
    }

    public function getCustomerNotificationEmailTemplate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CUSTOMER_NOTIFICATION_EMAIL_TEMPLATE, $storeId);
    }

    public function getNotifyCustomerOnAccountDeActivation($storeId = null)
    {
        return true; //@todo move it to system settings based
    }

    public function getCustomerDeActivationNotificationEmailTemplate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_CUSTOMER_DEACTIVATION_NOTIFICATION_EMAIL_TEMPLATE, $storeId);
    }
    /****************************************************************************************
     * RESTRICTED / ACCESSIBLE
     *****************************************************************************************/
    public function getRestrictionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTION_TYPE, $storeId);
    }

    public function getRestrictedAllowedCustomerGroups($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_CUSTOMER_GROUPS, $storeId);
        return explode(',', $value);
    }

    public function getRestrictedRedirectionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_REDIRECTION_TYPE, $storeId);
    }

    public function getRestrictedRedirectionTypeCms($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_REDIRECTION_TYPE_CMS, $storeId);
    }

    public function getRestrictedRedirectionTypeCustom($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_REDIRECTION_TYPE_CUSTOM, $storeId);
    }

    public function getRestrictedStoreErrorMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_STORE_ERROR_MESSAGE, $storeId);
    }

    public function getRestrictedCustomerGroupErrorMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_RESTRICTED_CUSTOMER_GROUP_ERROR_MESSAGE, $storeId);
    }

    public function getRestrictedAllowedCmsPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_CMS_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getRestrictedAllowedCategoryPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_CATEGORY_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getRestrictedAllowedProductPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_PRODUCT_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getRestrictedAllowedModulePages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_RESTRICTED_ALLOWED_MODULE_PAGES, $storeId);
        return explode("\n", $value);
    }

    /****************************************************************************************
     * ACCESSIBLE / RESTRICTED
     *****************************************************************************************/
    public function getAccessibleAllowedCustomerGroups($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_ALLOWED_CUSTOMER_GROUPS, $storeId);
        return explode(",", $value);
    }

    public function getAccessibleRedirectionType($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_REDIRECTION_TYPE, $storeId);
    }

    public function getAccessibleRedirectionTypeCms($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_REDIRECTION_TYPE_CMS, $storeId);
    }

    public function getAccessibleRedirectionTypeCustom($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_REDIRECTION_TYPE_CUSTOM, $storeId);
    }

    public function getAccessibleStoreErrorMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_STORE_ERROR_MESSAGE, $storeId);
    }

    public function getAccessibleHideProductPrices($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_PRODUCT_PRICES, $storeId);
    }

    public function getAccessibleHideProductPriceText($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_PRODUCT_PRICE_TEXT, $storeId);
    }

    public function getAccessibleHideProductPriceLink($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_PRODUCT_PRICE_LINK, $storeId);
    }

    public function getAccessibleHideAddToCart($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_ADD_TO_CART, $storeId);
    }

    public function getAccessibleHideAddToCartText($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_ADD_TO_CART_TEXT, $storeId);
    }

    public function getAccessibleHideAddToCartLink($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_ADD_TO_CART_LINK, $storeId);
    }

    public function getAccessibleHideCheckout($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_ACCESSIBLE_HIDE_CHECKOUT, $storeId);
    }

    public function getAccessibleRestrictedShipmentMethods($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_RESTRICTED_SHIPMENT_METHODS, $storeId);
        return explode(",", $value);
    }

    public function getAccessibleRestrictedPaymentMethods($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_RESTRICTED_PAYMENT_METHODS, $storeId);
        return explode(",", $value);
    }

    public function getAccessibleRestrictedCmsPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_RESTRICTED_CMS_PAGES, $storeId);
        return explode(',', $value);
    }

    public function getAccessibleRestrictedCategoryPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_RESTRICTED_CATEGORY_PAGES, $storeId);
        return explode(',', $value);
    }

    public function getAccessibleRestrictedProductPages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_RESTRICTED_PRODUCT_PAGES, $storeId);
        $value = preg_replace('/\s+/', '', $value);
        return explode(',', $value);
    }

    public function getAccessibleRestrictedModulePages($storeId = null)
    {
        $value = $this->getConfigValue(self::XML_PATH_ACCESSIBLE_RESTRICTED_MODULE_PAGES, $storeId);
        return explode("\n", $value);
    }
}