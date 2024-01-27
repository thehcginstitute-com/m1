<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
# 2024-01-27 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
class MagePsycho_Loginredirectpro_Helper_Data extends HCG\MagePsycho\Helper
{
    const MODULE_NAMESPACE_ALIAS = 'magepsycho_loginredirectpro';

    /**
     * Get config value
     *
     * @param $xmlPath
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigValue($xmlPath, $storeId = null)
    {
        return Mage::getStoreConfig(self::MODULE_NAMESPACE_ALIAS . '/' . $xmlPath, $storeId);
    }

    /**
     * Helper Config
     *
     * @return MagePsycho_Loginredirectpro_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper('magepsycho_loginredirectpro/config');
    }

    /**
     * Module Logging function
     *
     * @param $data
     * @param bool|false $includeSep
     */
    public function log($data, $includeSep = false)
    {
        if ( !$this->getConfig()->isLogEnabled()) {
            return;
        }

        if ($includeSep) {
            $data .= str_repeat('=', 70) . PHP_EOL;
        }
        Mage::log($data, null, self::MODULE_NAMESPACE_ALIAS . '.log', true);
    }

    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    /**
     * Load customer groups, ascending order
     *
     * @return mixed
     */
    public function getCustomerGroups()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
                              ->setOrder('customer_group_code', Varien_Data_Collection::SORT_ORDER_ASC)
                              ->setRealGroupsFilter()
                              ->loadData()
                              ->toOptionArray();
        return $customerGroups;
    }

	public function getCurrentGroupId()
	{
		$customer = $this->_getCustomerSession()->getCustomer();
		return $customer->getGroupId();
	}

	public function getMessage()
	{
		$message = base64_decode('WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgJ0N1c3RvbSBSZWRpcmVjdCBQcm8nIEV4dGVuc2lvbiBmb3IgZG9tYWluOiB7e0RPTUFJTn19LiBQbGVhc2UgZW50ZXIgYSB2YWxpZCBMaWNlbnNlIEtleSBmcm9tIFN5c3RlbSAmcmFxdW87IENvbmZpZ3VyYXRpb24gJnJhcXVvOyBNYWdlUHN5Y2hvIEV4dGVuc2lvbnMgJnJhcXVvOyBDdXN0b20gUmVkaXJlY3QgUHJvICZyYXF1bzsgR2VuZXJhbCBTZXR0aW5ncyAmcmFxdW87IExpY2Vuc2UgS2V5LiBJZiB5b3UgZG9uJ3QgaGF2ZSBvbmUsIHBsZWFzZSBwdXJjaGFzZSBhIHZhbGlkIGxpY2Vuc2UgZnJvbSA8YSBocmVmPSJodHRwOi8vd3d3Lm1hZ2Vwc3ljaG8uY29tL2NvbnRhY3RzIiB0YXJnZXQ9Il9ibGFuayI+d3d3Lm1hZ2Vwc3ljaG8uY29tPC9hPiBvciB5b3UgY2FuIGRpcmVjdGx5IGVtYWlsIHRvIDxhIGhyZWY9Im1haWx0bzppbmZvQG1hZ2Vwc3ljaG8uY29tIj5pbmZvQG1hZ2Vwc3ljaG8uY29tPC9hPg==');
		$message = str_replace('{{DOMAIN}}', $this->getDomain(), $message);
		return $message;
	}

	public function getDomain()
	{
        $domain		= Mage::getBaseUrl();
        $baseDomain = Mage::helper('magepsycho_loginredirectpro/url')->getBaseDomain($domain);

		return strtolower($baseDomain);
    }

    public function checkEntry($domain, $serial)
	{
        $salt = sha1(base64_decode('bG9naW5yZWRpcmVjdHBybw=='));
        if (sha1($salt . $domain . $this->mode()) == $serial) {
            return true;
        }

        return false;
    }

    public function isValid()
	{
        $temp = $this->temp();
        if (!$this->checkEntry($this->getDomain(), $temp)) {
            return false;
        }

        return true;
    }

	public function isActive()
	{
        return (bool)$this->getConfig()->isActive();
	}

    public function isFxnSkipped()
    {
        if (($this->isActive() && !$this->isValid()) || !$this->isActive()) {
            return true;
        }
        return false;
    }

    /**
     * Check if current request is API based
     *
     * @return bool
     */
    public function isApiRequest()
    {
        $isApiRequest = ($this->_getRequest()->getModuleName() === 'api' || $this->_getRequest()->getModuleName() === 'oauth')
            ? true
            : false;
        return $isApiRequest;
    }

    /**
     * Check if current code is executed from backend
     *
     * @return bool
     */
    public function isAdminArea()
    {
        if (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml') {
            return true;
        }

        return false;
    }

    public function checkVersion($version, $operator = '>=')
    {
        return version_compare(Mage::getVersion(), $version, $operator);
    }

    public function getExtensionVersion()
    {
        $moduleCode = 'MagePsycho_Loginredirectpro';
        return (string) $currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
    }

    /**
     * Get Store wise domain for System > Configuraiton settings
     *
     * @return string
     */
    public function getDomainFromSystemConfig()
    {
        $websiteCode = $this->_getRequest()->getParam('website');
        $storeCode   = $this->_getRequest()->getParam('store');

        $domain       = Mage::getConfig()->getNode('stores/' . $storeCode . '/web/unsecure/base_url');
        if (empty($domain)) {
            $domain     = Mage::getConfig()->getNode('websites/' . $websiteCode . '/web/unsecure/base_url');
            if (empty($domain)) {
                $domain      = Mage::getConfig()->getNode('default/web/unsecure/base_url');
            }
        }
        return $domain;
    }

    /**
     * @todo make it refreshable from admin
     *
     * @return GeoIP
     */
    public function loadGeoIp()
    {
        //REF: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
        include_once(Mage::getModuleDir('', 'MagePsycho_Loginredirectpro') . DS . 'Model' . DS . 'Api' . DS . 'GeoIP' . DS . 'GeoIP.inc');

        // Open Geo IP binary data file
        $geoIp = geoip_open(Mage::getModuleDir('', 'MagePsycho_Loginredirectpro') . DS . 'Model' . DS . 'Api' . DS . 'GeoIP' . DS . 'GeoIP.dat', GEOIP_STANDARD);

        return $geoIp;
    }

    public function getIpAddress()
    {
        $ip	 = Mage::helper('core/http')->getRemoteAddr();
        return $ip;
    }

    public function getCurrencyCode()
    {
        $geoIp     = $this->loadGeoIp();
        $ipAddress = $this->getIpAddress();

        // get country code from ip address
        $countryCode = geoip_country_code_by_addr($geoIp, $ipAddress);
        return $countryCode;
    }

    public function getRedirectToParamUrl()
    {
        $redirectToParamUrl = '';
        if ($redirectToParam = $this->getConfig()->getRedirectToParam()) {
            $redirectToParamUrl = $this->_getRequest()->getParam($redirectToParam);
        }
        return $redirectToParamUrl;
    }

    /**
     * Parse tabular array data
     *
     * @param $dbData
     * @param $against
     *
     * @return array
     */
    protected function _prepareGroupWiseData($dbData, $against)
    {
        $preparedData = array();
        if (empty($dbData)) {
            return $preparedData;
        }

        $dataArray = unserialize($dbData);
        if (empty($dataArray)) {
            return $preparedData;
        }

        foreach ($dataArray as $_data) {
            $groupId    = isset($_data['customer_group_id']) ? $_data['customer_group_id'] : null;
            $assoc      = isset($_data[$against]) ? $_data[$against] : null;
            if (!empty($groupId) && !empty($assoc)) {
                $preparedData[$groupId] = $assoc;
            }
        }
        return $preparedData;
    }

    public function getLoginUrlByGroup($groupId)
    {
        $groupToLoginData   = $this->getConfig()->getGroupLoginUrl();
        $groupToLoginUrls   = $this->_prepareGroupWiseData($groupToLoginData, 'login_redirect');
        $redirectUrl        = isset($groupToLoginUrls[$groupId]) ? $groupToLoginUrls[$groupId] : '';
        return $redirectUrl;
    }

    public function getLogoutUrlByGroup($groupId)
    {
        $groupToLogoutData   = $this->getConfig()->getGroupLogoutUrl();
        $groupToLogoutUrls   = $this->_prepareGroupWiseData($groupToLogoutData, 'logout_redirect');
        $redirectUrl        = isset($groupToLogoutUrls[$groupId]) ? $groupToLogoutUrls[$groupId] : '';
        return $redirectUrl;
    }

    public function getAccountUrlByGroup($groupId)
    {
        $groupToAccountData   = $this->getConfig()->getGroupAccountUrl();
        $groupToAccountUrls   = $this->_prepareGroupWiseData($groupToAccountData, 'account_redirect');
        $redirectUrl          = isset($groupToAccountUrls[$groupId]) ? $groupToAccountUrls[$groupId] : '';
        return $redirectUrl;
    }

    public function getAccountMessageByGroup($groupId)
    {
        $groupToAccountData         = $this->getConfig()->getGroupAccountMessage();
        $groupToAccountMessages     = $this->_prepareGroupWiseData($groupToAccountData, 'account_message');
        $message                    = isset($groupToAccountMessages[$groupId]) ? $groupToAccountMessages[$groupId] : '';
        return $message;
    }

    public function getAccountTemplateByGroup($groupId)
    {
        $groupToTemplateData        = $this->getConfig()->getGroupAccountTemplate();
        $groupToAccountTemplate     = $this->_prepareGroupWiseData($groupToTemplateData, 'account_template');
        $template                   = isset($groupToAccountTemplate[$groupId]) ? $groupToAccountTemplate[$groupId] : '';
        return $template;
    }

    public function getLoginRedirectionUrl($groupId = null)
    {
        if ( empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $redirectionUrl = $this->getLoginUrlByGroup($groupId);
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getConfig()->getDefaultLoginUrl();
        }
        $this->log('getLoginRedirectionUrl()::raw::' . $redirectionUrl);

        if ( !empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        if (empty($redirectionUrl)) {
            $redirectionUrl	= Mage::helper('customer')->getAccountUrl();
        }
        $this->log('getLoginRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function getLogoutRedirectionUrl($groupId = null)
    {
        if ( empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $redirectionUrl = $this->getLogoutUrlByGroup($groupId);
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getConfig()->getDefaultLogoutUrl();
        }
        $this->log('getLogoutRedirectionUrl()::raw::' . $redirectionUrl);

        if ( !empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        if (empty($redirectionUrl)) {
            $redirectionUrl	= Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
        }
        $this->log('getLogoutRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function getAccountRedirectionUrl($groupId = null)
    {
        if ( empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $redirectionUrl = $this->getAccountUrlByGroup($groupId);
        if (empty($redirectionUrl)) {
            $redirectionUrl = $this->getConfig()->getDefaultAccountUrl();
        }
        $this->log('getAccountRedirectionUrl()::raw::' . $redirectionUrl);

        if ( !empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        if (empty($redirectionUrl)) {
            $redirectionUrl	= Mage::helper('customer')->getAccountUrl();
        }
        $this->log('getAccountRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function getAccountSuccessMessage($groupId = null)
    {
        if ( empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $successMessage = $this->getAccountMessageByGroup($groupId);
        if (empty($successMessage)) {
            $successMessage = $this->getConfig()->getDefaultAccountMessage();
        }
        $this->log('getAccountSuccessMessage()::' . $successMessage);
        return $successMessage;
    }

    public function getAccountTemplate($groupId = null)
    {
        if ( empty($groupId)) {
            $groupId = $this->getCurrentGroupId();
        }

        $template = $this->getAccountTemplateByGroup($groupId);
        $this->log('getAccountTemplate()::' . $template);
        return $template;
    }

    public function isAccountGroupTemplateEmpty()
    {
        $groupToTemplateData = $this->getConfig()->getGroupAccountTemplate();
        if (empty($groupToTemplateData) || $groupToTemplateData == 'a:0:{}') {
            return true;
        }
        return false;
    }

    public function getNewsletterRedirectionUrl()
    {
        $redirectionUrl = $this->getConfig()->getNewsletterUrl();
        $this->log('getNewsletterRedirectionUrl()::raw::' . $redirectionUrl);

        if ( !empty($redirectionUrl)) {
            $redirectionUrl = $this->_processUrlParams($redirectionUrl);
        }

        // default Url
        /*if (empty($redirectionUrl)) {
            $redirectionUrl	= $this->getRefererUrl();
        }*/

        $this->log('getNewsletterRedirectionUrl()::final::' . $redirectionUrl);
        return $redirectionUrl;
    }

    public function isAbsoluteUrl($url)
    {
        return stripos($url, 'http://') !== false || stripos($url, 'https://') !== false;
    }

    public function convertRelToAbsoulteUrl($relUrl)
    {
        if ($this->isAbsoluteUrl($relUrl)) {
            return $relUrl;
        }
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true) . ltrim($relUrl, '/');
    }

    public function unsetCustomerLogoutChildIf()
    {
        if ( !$this->isFxnSkipped() && !$this->getConfig()->getRemoveLogoutIntermediate()) {
            return 'customer_logout';
        } else {
            return 'customer_logout';
        }
    }

    public function switchCustomerLogoutTemplateIf()
    {
        if ( !$this->isFxnSkipped() && !$this->getConfig()->getRemoveLogoutIntermediate()) {
            return 'magepsycho/loginredirectpro/customer/logout.phtml';
        } else {
            return 'customer/logout.phtml';
        }
    }

    /**
     * Parse redirection params and prepare the final url
     *
     * @param $redirectionUrl
     *
     * @return string
     */
    protected function _processUrlParams($redirectionUrl)
    {
        // these variables represent a complete url
        if (stripos($redirectionUrl, '{{assigned_base_url}}') !== false) { //Assigned base url
            $redirectionUrl	= str_replace('{{assigned_base_url}}', $this->getAssignedBaseUrl(), $redirectionUrl);
        }
        if (stripos($redirectionUrl, '{{referer}}') !== false) { //Referer
            $redirectionUrl	= str_replace('{{referer}}', $this->_getCustomerSession()->getBeforeAuthUrlClrp(), $redirectionUrl);
        }
        if (stripos($redirectionUrl, '{{redirect_to}}') !== false) { //Redirect to using query param
            $redirectionUrl = str_replace('{{redirect_to}}', $this->_getCustomerSession()->getRedirectToUrlClrp(), $redirectionUrl);
        }

        //convert relative to absolute url
        $redirectionUrl = $this->convertRelToAbsoulteUrl($redirectionUrl);

        // these variables are part of a url
        if (stripos($redirectionUrl, '{{ip}}') !== false) { //Ip Address
            $redirectionUrl = str_replace('{{ip}}', $this->getIpAddress(), $redirectionUrl);
        }
        if (stripos($redirectionUrl, '{{country_code}}') !== false) { //Ip Address
            $redirectionUrl = str_replace('{{country_code}}', $this->getCurrencyCode(), $redirectionUrl);
        }

        if ($this->_getCustomerSession()->isLoggedIn()) {
            $customer = $this->_getCustomerSession()->getCustomer();
            if (stripos($redirectionUrl, '{{user_name}}') !== false) { //User Full Name
                $redirectionUrl = str_replace('{{user_name}}', $customer->getName(), $redirectionUrl);
            }
            if (stripos($redirectionUrl, '{{user_email}}') !== false) { //User Email
                $redirectionUrl = str_replace('{{user_email}}', $customer->getEmail(), $redirectionUrl);
            }
            if (stripos($redirectionUrl, '{{user_id}}') !== false) { //User Id
                $redirectionUrl = str_replace('{{user_id}}', $customer->getId(), $redirectionUrl);
            }
            if (stripos($redirectionUrl, '{{user_group_id}}') !== false) { //User Group Id
                $redirectionUrl	= str_replace('{{user_group_id}}', $customer->getGroupId(), $redirectionUrl);
            }
        }

        $redirectionUrl = trim($redirectionUrl, '/');
        return $redirectionUrl;
    }

    /**
     * Get assigned website base url of a customer
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    public function getAssignedBaseUrl()
    {
        $assignedBaseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
        if ($this->_getCustomerSession()->isLoggedIn()) {
            $customer           = $this->_getCustomerSession()->getCustomer();
            $website            = Mage::app()->getWebsite($customer->getWebsiteId());
            $assignedBaseUrl    = $website->getDefaultStore()->getBaseUrl();
        }
        return $assignedBaseUrl;
    }

    /**
     * Identify referer url via all accepted methods (HTTP_REFERER, regular or base64-encoded request param)
     *
     * @see Mage_Core_Controller_Varien_Action::_getRefererUrl()
     * @return string
     */
    public function getRefererUrl()
    {
        $refererUrl = $this->_getRequest()->getServer('HTTP_REFERER');
        if ($url = $this->_getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_REFERER_URL)) {
            $refererUrl = $url;
        }
        if ($url = $this->_getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_BASE64_URL)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }
        if ($url = $this->_getRequest()->getParam(Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED)) {
            $refererUrl = Mage::helper('core')->urlDecodeAndEscape($url);
        }

        if (!$this->_isUrlInternal($refererUrl)) {
            $refererUrl = Mage::app()->getStore()->getBaseUrl();
        }
        return $refererUrl;
    }

    /**
     * Check url to be used as internal
     *
     * @see Mage_Core_Controller_Varien_Action::_isUrlInternal()
     * @param   string $url
     * @return  bool
     */
    protected function _isUrlInternal($url)
    {
        if (strpos($url, 'http') !== false) {
            /**
             * Url must start from base secure or base unsecure url
             */
            if ((strpos($url, Mage::app()->getStore()->getBaseUrl()) === 0)
                || (strpos($url, Mage::app()->getStore()->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK, true)) === 0)
            ) {
                return true;
            }
        }
        return false;
    }
}