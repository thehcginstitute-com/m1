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
	/**
	 * Module Logging function
	 *
	 * @param $data
	 * @param bool|false $includeSep
	 */
	function log($data, $includeSep = false)
	{
		if ( !$this->cfgH()->isLogEnabled()) {
			return;
		}

		if ($includeSep) {
			$data .= str_repeat('=', 70) . PHP_EOL;
		}
		Mage::log($data, null, $this->moduleMf() . '.log', true);
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
	function getCustomerGroups()
	{
		$customerGroups = Mage::getResourceModel('customer/group_collection')
							  ->setOrder('customer_group_code', Varien_Data_Collection::SORT_ORDER_ASC)
							  ->setRealGroupsFilter()
							  ->loadData()
							  ->toOptionArray();
		return $customerGroups;
	}

	function getCurrentGroupId()
	{
		$customer = $this->_getCustomerSession()->getCustomer();
		return $customer->getGroupId();
	}

	/**
	 * Check if current request is API based
	 *
	 * @return bool
	 */
	function isApiRequest()
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
	function isAdminArea()
	{
		if (Mage::app()->getStore()->isAdmin() || Mage::getDesign()->getArea() == 'adminhtml') {
			return true;
		}

		return false;
	}

	function checkVersion($version, $operator = '>=')
	{
		return version_compare(Mage::getVersion(), $version, $operator);
	}

	function getExtensionVersion()
	{
		$moduleCode = 'MagePsycho_Loginredirectpro';
		return (string) $currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
	}

	/**
	 * Get Store wise domain for System > Configuraiton settings
	 *
	 * @return string
	 */
	function getDomainFromSystemConfig()
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
	function loadGeoIp()
	{
		//REF: http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz
		include_once(Mage::getModuleDir('', 'MagePsycho_Loginredirectpro') . DS . 'Model' . DS . 'Api' . DS . 'GeoIP' . DS . 'GeoIP.inc');

		// Open Geo IP binary data file
		$geoIp = geoip_open(Mage::getModuleDir('', 'MagePsycho_Loginredirectpro') . DS . 'Model' . DS . 'Api' . DS . 'GeoIP' . DS . 'GeoIP.dat', GEOIP_STANDARD);

		return $geoIp;
	}

	function getIpAddress()
	{
		$ip	 = Mage::helper('core/http')->getRemoteAddr();
		return $ip;
	}

	function getCurrencyCode()
	{
		$geoIp     = $this->loadGeoIp();
		$ipAddress = $this->getIpAddress();

		// get country code from ip address
		$countryCode = geoip_country_code_by_addr($geoIp, $ipAddress);
		return $countryCode;
	}

	function getRedirectToParamUrl()
	{
		$redirectToParamUrl = '';
		if ($redirectToParam = $this->cfgH()->getRedirectToParam()) {
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

	function getLoginUrlByGroup($groupId)
	{
		$groupToLoginData   = $this->cfgH()->getGroupLoginUrl();
		$groupToLoginUrls   = $this->_prepareGroupWiseData($groupToLoginData, 'login_redirect');
		$redirectUrl        = isset($groupToLoginUrls[$groupId]) ? $groupToLoginUrls[$groupId] : '';
		return $redirectUrl;
	}

	function getLogoutUrlByGroup($groupId)
	{
		$groupToLogoutData   = $this->cfgH()->getGroupLogoutUrl();
		$groupToLogoutUrls   = $this->_prepareGroupWiseData($groupToLogoutData, 'logout_redirect');
		$redirectUrl        = isset($groupToLogoutUrls[$groupId]) ? $groupToLogoutUrls[$groupId] : '';
		return $redirectUrl;
	}

	function getAccountUrlByGroup($groupId)
	{
		$groupToAccountData   = $this->cfgH()->getGroupAccountUrl();
		$groupToAccountUrls   = $this->_prepareGroupWiseData($groupToAccountData, 'account_redirect');
		$redirectUrl          = isset($groupToAccountUrls[$groupId]) ? $groupToAccountUrls[$groupId] : '';
		return $redirectUrl;
	}

	function getAccountMessageByGroup($groupId)
	{
		$groupToAccountData         = $this->cfgH()->getGroupAccountMessage();
		$groupToAccountMessages     = $this->_prepareGroupWiseData($groupToAccountData, 'account_message');
		$message                    = isset($groupToAccountMessages[$groupId]) ? $groupToAccountMessages[$groupId] : '';
		return $message;
	}

	function getAccountTemplateByGroup($groupId)
	{
		$groupToTemplateData        = $this->cfgH()->getGroupAccountTemplate();
		$groupToAccountTemplate     = $this->_prepareGroupWiseData($groupToTemplateData, 'account_template');
		$template                   = isset($groupToAccountTemplate[$groupId]) ? $groupToAccountTemplate[$groupId] : '';
		return $template;
	}

	function getLoginRedirectionUrl($groupId = null)
	{
		if ( empty($groupId)) {
			$groupId = $this->getCurrentGroupId();
		}

		$redirectionUrl = $this->getLoginUrlByGroup($groupId);
		if (empty($redirectionUrl)) {
			$redirectionUrl = $this->cfgH()->getDefaultLoginUrl();
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

	function getLogoutRedirectionUrl($groupId = null)
	{
		if ( empty($groupId)) {
			$groupId = $this->getCurrentGroupId();
		}

		$redirectionUrl = $this->getLogoutUrlByGroup($groupId);
		if (empty($redirectionUrl)) {
			$redirectionUrl = $this->cfgH()->getDefaultLogoutUrl();
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

	function getAccountRedirectionUrl($groupId = null)
	{
		if ( empty($groupId)) {
			$groupId = $this->getCurrentGroupId();
		}

		$redirectionUrl = $this->getAccountUrlByGroup($groupId);
		if (empty($redirectionUrl)) {
			$redirectionUrl = $this->cfgH()->getDefaultAccountUrl();
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

	function getAccountSuccessMessage($groupId = null)
	{
		if ( empty($groupId)) {
			$groupId = $this->getCurrentGroupId();
		}

		$successMessage = $this->getAccountMessageByGroup($groupId);
		if (empty($successMessage)) {
			$successMessage = $this->cfgH()->getDefaultAccountMessage();
		}
		$this->log('getAccountSuccessMessage()::' . $successMessage);
		return $successMessage;
	}

	function getAccountTemplate($groupId = null)
	{
		if ( empty($groupId)) {
			$groupId = $this->getCurrentGroupId();
		}

		$template = $this->getAccountTemplateByGroup($groupId);
		$this->log('getAccountTemplate()::' . $template);
		return $template;
	}

	function isAccountGroupTemplateEmpty()
	{
		$groupToTemplateData = $this->cfgH()->getGroupAccountTemplate();
		if (empty($groupToTemplateData) || $groupToTemplateData == 'a:0:{}') {
			return true;
		}
		return false;
	}

	function getNewsletterRedirectionUrl()
	{
		$redirectionUrl = $this->cfgH()->getNewsletterUrl();
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

	function isAbsoluteUrl($url)
	{
		return stripos($url, 'http://') !== false || stripos($url, 'https://') !== false;
	}

	function convertRelToAbsoulteUrl($relUrl)
	{
		if ($this->isAbsoluteUrl($relUrl)) {
			return $relUrl;
		}
		return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true) . ltrim($relUrl, '/');
	}

	function unsetCustomerLogoutChildIf()
	{
		if ( !$this->isFxnSkipped() && !$this->cfgH()->getRemoveLogoutIntermediate()) {
			return 'customer_logout';
		} else {
			return 'customer_logout';
		}
	}

	function switchCustomerLogoutTemplateIf()
	{
		if ( !$this->isFxnSkipped() && !$this->cfgH()->getRemoveLogoutIntermediate()) {
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
	function getAssignedBaseUrl()
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
	function getRefererUrl()
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

	/**
	 * 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
	 * @override
	 * @see HCG\MagePsycho\Helper::moduleMf()
	 * @used-by HCG\MagePsycho\Helper::cfg()
	 * @used-by self::log()
	 */
	final protected function moduleMf():string {return 'magepsycho_loginredirectpro';}
}