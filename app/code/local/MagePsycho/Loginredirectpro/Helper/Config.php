<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Helper_Config extends MagePsycho_Loginredirectpro_Helper_Data
{
    /* General */
    const XML_PATH_ACTIVE                       = 'option/active';
    const XML_PATH_ENABLE_LOG                   = 'option/enable_log';
    const XML_PATH_DOMAIN_TYPE                  = 'option/domain_type';

    /* Login */
    const XML_PATH_DEFAULT_LOGIN_URL            = 'login_settings/default_login_url';
    const XML_PATH_GROUP_LOGIN_URL              = 'login_settings/group_login_url';

    /* Logout */
    const XML_PATH_DEFAULT_LOGOUT_URL           = 'logout_settings/default_logout_url';
    const XML_PATH_GROUP_LOGOUT_URL             = 'logout_settings/group_logout_url';
    const XML_PATH_LOGOUT_REMOVE_INTER          = 'logout_settings/remove_logout_intermediate';
    const XML_PATH_LOGOUT_MESSAGE               = 'logout_settings/logout_custom_message';
    const XML_PATH_LOGOUT_DELAY                 = 'logout_settings/logout_delay_time';

    /* New Account */
    const XML_PATH_DEFAULT_ACCOUNT_URL          = 'account_settings/default_account_url';
    const XML_PATH_GROUP_ACCOUNT_URL            = 'account_settings/group_account_url';
    const XML_PATH_GROUP_ACCOUNT_TEMPLATE       = 'account_settings/group_account_template';
    const XML_PATH_DEFAULT_ACCOUNT_MESSAGE      = 'account_settings/default_account_message';
    const XML_PATH_GROUP_ACCOUNT_MESSAGE        = 'account_settings/group_account_message';

    /* Misc */
    const XML_PATH_NEWSLETTER_URL               = 'misc_settings/newsletter_url';
    const XML_PATH_REDIRECT_TO_PARAM            = 'misc_settings/redirect_to_param';


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

    /****************************************************************************************
     * LOGIN
     *****************************************************************************************/
    public function getDefaultLoginUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_LOGIN_URL, $storeId);
    }

    public function getGroupLoginUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_LOGIN_URL, $storeId);
    }


    /****************************************************************************************
     * LOGOUT
     *****************************************************************************************/
    public function getDefaultLogoutUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_LOGOUT_URL, $storeId);
    }

    public function getGroupLogoutUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_LOGOUT_URL, $storeId);
    }

    public function getRemoveLogoutIntermediate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LOGOUT_REMOVE_INTER, $storeId);
    }

    public function getLogoutMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LOGOUT_MESSAGE, $storeId);
    }

    public function getLogoutDelay($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_LOGOUT_DELAY, $storeId);
    }

    /****************************************************************************************
     * NEW ACCOUNT
     *****************************************************************************************/
    public function getDefaultAccountUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_ACCOUNT_URL, $storeId);
    }

    public function getGroupAccountUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_ACCOUNT_URL, $storeId);
    }

    public function getGroupAccountTemplate($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_ACCOUNT_TEMPLATE, $storeId);
    }

    public function getDefaultAccountMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_DEFAULT_ACCOUNT_MESSAGE, $storeId);
    }

    public function getGroupAccountMessage($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_GROUP_ACCOUNT_MESSAGE, $storeId);
    }

    /****************************************************************************************
     * MISC
     *****************************************************************************************/
    public function getNewsletterUrl($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_NEWSLETTER_URL, $storeId);
    }

    public function getRedirectToParam($storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_REDIRECT_TO_PARAM, $storeId);
    }

}