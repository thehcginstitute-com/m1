<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MODULE_NAMESPACE_ALIAS = 'magepsycho_storerestrictionpro';

    public function getConfigValue($xmlPath, $storeId = null)
    {
        return Mage::getStoreConfig(self::MODULE_NAMESPACE_ALIAS . '/' . $xmlPath, $storeId);
    }

    public function getConfig()
    {
        return Mage::helper('magepsycho_storerestrictionpro/config');
    }

    public function log($data, $includeSep = false)
    {
        if ( !$this->getConfig()->isLogEnabled() || !$this->isActive()) {
            return;
        }
        if ($includeSep) {
            $separator = '==========================================================================';
            Mage::log($separator, null, self::MODULE_NAMESPACE_ALIAS . '.log', true);
        }
        Mage::log($data, null, self::MODULE_NAMESPACE_ALIAS . '.log', true);
    }

	public function isApiRequest()
	{
		$request = Mage::app()->getRequest();
        $isApiRequest = ($request->getModuleName() === 'api' || $request->getModuleName() === 'oauth') ? true : false;
		return $isApiRequest;
	}

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
        $moduleCode = 'MagePsycho_Storerestrictionpro';
        return (string)$currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
    }

	function __construct()
	{
        $field = base64_decode('ZG9tYWluX3R5cGU=');
        if ($this->getConfigValue('option/' . $field) == 1) {
            $key        = base64_decode('cHJvZF9saWNlbnNl');
            $this->mode = base64_decode('cHJvZHVjdGlvbg==');
        } else {
            $key        = base64_decode('ZGV2X2xpY2Vuc2U=');
            $this->mode = base64_decode('ZGV2ZWxvcG1lbnQ=');
        }
        $this->temp = $this->getConfigValue('option/' . $key);
	}

	public function getMessage()
	{
		$message = base64_decode(
			'WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgJ1N0b3JlIFJlc3RyaWN0aW9uIFBybycgRXh0ZW5zaW9uIGZvciBkb21haW46IHt7RE9NQUlOfX0uIFBsZWFzZSBlbnRlciBhIHZhbGlkIExpY2Vuc2UgS2V5IGZyb20gU3lzdGVtICZyYXF1bzsgQ29uZmlndXJhdGlvbiAmcmFxdW87IE1hZ2VQc3ljaG8gRXh0ZW5zaW9ucyAmcmFxdW87IFN0b3JlIFJlc3RyaWN0aW9uIFBybyAmcmFxdW87IExpY2Vuc2UgS2V5LiBJZiB5b3UgZG9uJ3QgaGF2ZSBvbmUsIHBsZWFzZSBwdXJjaGFzZSBhIHZhbGlkIGxpY2Vuc2UgZnJvbSA8YSBocmVmPSJodHRwOi8vd3d3Lm1hZ2Vwc3ljaG8uY29tL2NvbnRhY3RzIiB0YXJnZXQ9Il9ibGFuayI+d3d3Lm1hZ2Vwc3ljaG8uY29tPC9hPiBvciB5b3UgY2FuIGRpcmVjdGx5IGVtYWlsIHRvIDxhIGhyZWY9Im1haWx0bzppbmZvQG1hZ2Vwc3ljaG8uY29tIj5pbmZvQG1hZ2Vwc3ljaG8uY29tPC9hPg=='
		);
		$message = str_replace('{{DOMAIN}}', $this->getDomain(), $message);

		return $message;
	}

	public function getDomain()
	{
		$domain     = Mage::getBaseUrl();
		$baseDomain = Mage::helper('magepsycho_storerestrictionpro/url')->getBaseDomain($domain);

        return strtolower($baseDomain);
	}

    public function checkEntry($domain, $serial)
    {
        $salt = sha1(base64_decode('c3RvcmVyZXN0cmljdGlvbnBybw=='));
        if(sha1($salt . $domain . $this->mode) == $serial) {
            return true;
        }

        return false;
    }

	public function isValid()
	{
		$temp = $this->temp;
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

    public function skipRestrictionByDefault()
    {
        $isCustomerNonLoggedInIndexPage = (!Mage::getSingleton('customer/session')->isLoggedIn() && $this->checkPageUrl('customer', 'account', 'index')) ? true : false; //@tweak for forgotpassword;
        if ($this->isFxnSkipped() || $isCustomerNonLoggedInIndexPage || $this->isLoginPage() || $this->isLogoutPage() || $this->isForgotPasswordPage() || $this->isAccountCreatePage() || $this->is404ErrorPage() || $this->isCookiePage() || $this->isApiRequest()) {
            return true;
        }
        return false;
    }

    public function checkPageUrl($moduleName, $controllerName, $actionName)
    {
        $request         = Mage::app()->getRequest();
        $_moduleName     = strtolower($request->getModuleName());
        $_controllerName = strtolower($request->getControllerName());
        $_actionName     = strtolower($request->getActionName());
        if (strtolower($moduleName) == $_moduleName
            && strtolower($controllerName) == $_controllerName
            && strtolower($actionName) == $_actionName
        ) {
            return true;
        } else {
            return false;
        }
    }

    public function _checkControllerAction($controller, $action)
    {
        $request = Mage::app()->getRequest();
        return $this->checkPageUrl($request->getModuleName(), $controller, $action);
    }

    public function switchAccountLoginTemplateIf()
    {
        if ( ! $this->isFxnSkipped() && $this->getConfig()->getNewAccountRegistrationOption() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Newaccounttypes::NEW_ACCOUNT_REGISTRATION_DISABLED) {
            return 'magepsycho/storerestrictionpro/customer/form/login.phtml';
        } else {
            return 'persistent/customer/form/login.phtml';
        }
    }

    public function switchCheckoutLoginTemplateIf()
    {
        if ( ! $this->isFxnSkipped() && $this->getConfig()->getNewAccountRegistrationOption() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Newaccounttypes::NEW_ACCOUNT_REGISTRATION_DISABLED) {
            return 'magepsycho/storerestrictionpro/checkout/onepage/login.phtml';
        } else {
            return 'persistent/checkout/onepage/login.phtml';
        }
    }

    public function getDomainFromSystemConfig()
    {
        $websiteCode = Mage::app()->getRequest()->getParam('website');
        $storeCode   = Mage::app()->getRequest()->getParam('store');

        $domain       = Mage::getConfig()->getNode('stores/' . $storeCode . '/web/unsecure/base_url');
        if (empty($domain)) {
            $domain     = Mage::getConfig()->getNode('websites/' . $websiteCode . '/web/unsecure/base_url');
            if (empty($domain)) {
                $domain      = Mage::getConfig()->getNode('default/web/unsecure/base_url');
            }
        }
        return $domain;
    }

    /******************************************************************************************************
     * REGISTRATION / ACTIVATION
     *****************************************************************************************************/

	public function isNewAccountActivationEnabled()
	{
		if ($this->getConfig()->getNewAccountActivationRequired()) {
			return true;
		} else {
			return false;
		}
	}

    public function isAccountRegistrationDisabled()
    {
        $registrationType = $this->getConfig()->getNewAccountRegistrationOption();
        if ($registrationType == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Newaccounttypes::NEW_ACCOUNT_REGISTRATION_DISABLED) {
            return true;
        } else {
            return false;
        }
    }

    public function skipAccountActivationFxn()
    {
        //skip if account registration is disabled || account activation is not required
        $skipCheck = $this->isFxnSkipped() || !$this->isNewAccountActivationEnabled() || $this->isAccountRegistrationDisabled();
        return $skipCheck;
    }

    public function isAccountRegistrationPage()
    {
        $accountCreate = $this->checkPageUrl('customer', 'account', 'create');
        $accountCreatepost = $this->checkPageUrl('customer', 'account', 'createpost');
        if ($accountCreate || $accountCreatepost) {
            return true;
        } else {
            return false;
        }
    }

    public function getNonActivatedLandingPage()
    {
        $redirectionType = $this->getConfig()->getNewAccountActivationRedirectionType();
        switch ($redirectionType) {
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CMS:
                $cmsIdentifier = trim($this->getConfig()->getNewAccountActivationRedirectionTypeCms(), '/');
                $landingUrl = Mage::getUrl($cmsIdentifier);
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CUSTOM:
                $customPage = trim($this->getConfig()->getNewAccountActivationRedirectionTypeCustom(), '/');
                $landingUrl = Mage::getUrl($customPage);
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_LOGIN:
            default:
                $landingUrl = Mage::getUrl('customer/account/login');
                break;
        }
        return $landingUrl;
    }

    public function getAccountActivationDefaultStatus($groupId, $storeId)
    {
        $isDefaultActive = $this->getConfig()->getNewAccountActivationByDefaultFrontend($storeId);
        if (!$isDefaultActive) {
            $activationRequiredGroups = $this->getConfig()->getActivationRequiredCustomerGroups($storeId);
            $activationRequiredGroupsArray = explode(',', $activationRequiredGroups);
            $isActive = in_array($groupId, $activationRequiredGroupsArray) || in_array(-1, $activationRequiredGroupsArray) ? false : true;
        } else {
            $isActive = $isDefaultActive;
        }

        return $isActive;
    }

    public function getCustomerStoreId(Mage_Customer_Model_Customer $customer)
    {
        if (!($storeId = $customer->getSendemailStoreId())) {
            $storeId = $customer->getStoreId();
            if (!$storeId && $customer->getWebsiteId()) {
                if ($store = Mage::app()->getWebsite($customer->getWebsiteId())->getDefaultStore()) {
                    $storeId = $store->getId();
                }
            }
        }

        return $storeId;
    }

    public function sendAdminNotificationEmail(Mage_Customer_Model_Customer $customer)
    {
        $storeId = $this->getCustomerStoreId($customer);
        $notifyAdminOnRegistration = (bool)$this->getConfig()->getNotifyAdminOnCustomerRegistration($storeId);
        $this->log('$notifyAdminOnRegistration::' . $notifyAdminOnRegistration);
        if ($notifyAdminOnRegistration) {
            $emailsData = $this->getConfig()->getCustomerRegistrationNotificationAdminEmails($storeId);
            $to = array();
            if (!empty($emailsData)) {
                $emailsData = preg_replace('/\s+/', '', $emailsData);
                $to = explode(',', $emailsData);
            }
            $this->log('$to::' . print_r($to, true));
            $template = $this->getConfig()->getAdminNotificationEmailTemplate($storeId);
            $this->_sendNotificationEmail($to, $customer, $template);
        }

        return $this;
    }

    public function sendCustomerNotificationEmail(Mage_Customer_Model_Customer $customer)
    {
        $storeId =  $this->getCustomerStoreId($customer);
        $notifyCustomerOnActivation = (bool)$this->getConfig()->getNotifyCustomerOnAccountActivation($storeId);
        $this->log('$notifyCustomerOnActivation::' . $notifyCustomerOnActivation);
        if ($notifyCustomerOnActivation) {
            $to = array(
                array(
                    'name'  => $customer->getName(),
                    'email' => $customer->getEmail(),
                )
            );
            $template = $this->getConfig()->getCustomerNotificationEmailTemplate($storeId);
            $this->_sendNotificationEmail($to, $customer, $template);
        }

        return $this;
    }

    public function sendCustomerDeActivationNotificationEmail(Mage_Customer_Model_Customer $customer)
    {
        $storeId =  $this->getCustomerStoreId($customer);
        $notifyCustomerOnDeActivation = (bool)$this->getConfig()->getNotifyCustomerOnAccountDeActivation($storeId);
        $this->log('$notifyCustomerOnDeActivation::' . $notifyCustomerOnDeActivation);
        if ($notifyCustomerOnDeActivation) {
            $to = array(
                array(
                    'name'  => $customer->getName(),
                    'email' => $customer->getEmail(),
                )
            );
            $template = $this->getConfig()->getCustomerDeActivationNotificationEmailTemplate($storeId);
            $this->_sendNotificationEmail($to, $customer, $template);
        }

        return $this;
    }

    protected function _sendNotificationEmail($to, $customer, $template)
    {
        if (!$to) {
            return;
        }

        $storeId   = $this->getCustomerStoreId($customer);
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */

        $this->log('$template::' . $template);

        $sendTo = array();
        foreach ($to as $recipient) {
            if (is_array($recipient)) {
                $sendTo[] = $recipient;
            } else {
                $sendTo[] = array(
                    'email' => $recipient,
                    'name'  => null,
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(Mage_Customer_Model_Customer::XML_PATH_REGISTER_EMAIL_IDENTITY, $storeId),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'customer' => $customer,
                        'shipping' => $customer->getPrimaryShippingAddress(),
                        'billing'  => $customer->getPrimaryBillingAddress(),
                        'store'    => Mage::app()->getStore($storeId),
                    )
                );
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    public function _getEmails($configPath)
    {
        $data = $this->getConfigValue($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }

        return false;
    }

    /******************************************************************************************************
     * STORE RESTRICTION - RESTRICTED / ACCESSIBLE
     *****************************************************************************************************/
    public function getCustomerGroups()
    {
        $customerGroups = Mage::getResourceModel('customer/group_collection')
            ->setRealGroupsFilter()
            ->loadData()
            ->toOptionArray();

        return $customerGroups;
    }

    public function getRestrictedLandingPage()
    {
        $redirectionType = $this->getConfig()->getRestrictedRedirectionType();
        switch ($redirectionType) {
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CMS:
                //@todo check if the CMS page belongs to the store or not
                $cmsIdentifier = trim($this->getConfig()->getRestrictedRedirectionTypeCms(), '/');
                if ($cmsIdentifier == $this->_getHomepageIdentifier()) {
                    $cmsIdentifier = ''; //remove /home from url
                }
                $landingUrl = Mage::getUrl($cmsIdentifier);
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CUSTOM:
                $customPage = trim($this->getConfig()->getRestrictedRedirectionTypeCustom(), '/');
                $landingUrl = Mage::getUrl($customPage);
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_LOGIN:
            default:
                $landingUrl = Mage::getUrl('customer/account/login');
                break;
        }
        return $landingUrl;
    }

    protected function _isHomepage()
    {
        if (Mage::app()->getFrontController()->getAction()->getFullActionName() == 'cms_index_index') {
            return true;
        } else {
            return false;
        }
    }

    protected function _getHomepageIdentifier()
    {
        return Mage::getStoreConfig('web/default/cms_home_page');
    }

    public function isRestrictedCmsPageAccessible()
    {
        $canAccess = false;
        $currentIdentifier = '';
        if ($this->_isHomepage()) {
            $currentIdentifier = $this->_getHomepageIdentifier();
            $this->log('Homepage::$currentIdentifier::' . $currentIdentifier);
        } else {
            $request =  Mage::app()->getRequest();
            $pageId  = $request->getParam('page_id', $request->getParam('id', false));
            if ($pageId) {
                $page = Mage::getModel('cms/page')->load($pageId);
                $currentIdentifier = $page->getIdentifier();
            }
            $this->log('Other CMS::$currentIdentifier::' . $currentIdentifier);
        }

        $allowedCmsPages   = $this->getConfig()->getRestrictedAllowedCmsPages();
        if ($this->getConfig()->getRestrictedRedirectionType() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CMS) {
            $cmsLandingPage    = $this->getConfig()->getRestrictedRedirectionTypeCms();
            $allowedCmsPages = array_merge($allowedCmsPages, array($cmsLandingPage));
        }
        $this->log('$allowedCmsPages::' . implode(', ', $allowedCmsPages));

        if (!empty($currentIdentifier) && in_array($currentIdentifier, $allowedCmsPages)) {
            $canAccess = true;
        }
        return $canAccess;
    }

    public function isRestrictedCategoryPageAccessible()
    {
        $canAccess = false;
        $request = Mage::app()->getRequest();
        $currentCategoryId = $request->getParam('id');
        $this->log('$currentCategoryId::' . $currentCategoryId);
        $allowedCategories = $this->getConfig()->getRestrictedAllowedCategoryPages();
        $this->log('$allowedCategories::' . implode(', ', $allowedCategories));
        if (in_array($currentCategoryId, $allowedCategories)) {
            $canAccess = true;
        }
        return $canAccess;
    }

    public function isRestrictedProductPageAccessible()
    {
        $canAccess          = false;
        $request            = Mage::app()->getRequest();
        $currentProductId   = $request->getParam('id');
        $currentProductSku  = Mage::getModel('catalog/product')->load($currentProductId)->getSku();
        $this->log('$currentProductSku::' . $currentProductSku);
        $allowedProducts    = $this->getConfig()->getRestrictedAllowedProductPages();
        $this->log('$allowedProducts::' . implode(',', $allowedProducts));
        if (in_array($currentProductSku, $allowedProducts)) {
            $canAccess = true;
        }
        return $canAccess;
    }

    public function isRestrictedModulePageAccessible()
    {
        $canAccess          = false;
        $request               = Mage::app()->getRequest();
        $currentModuleName     = $request->getModuleName();
        $currentControllerName = $request->getControllerName();
        $currentActionName     = $request->getActionName();

        $this->log(
            '$currentModuleName::' . $currentModuleName . ', $currentControllerName::' . $currentControllerName . ', $currentActionName::' . $currentActionName
        );

        $allowedModules = $this->getConfig()->getRestrictedAllowedModulePages();
        $this->log('$allowedModules::' . implode(', ', $allowedModules));
        foreach ($allowedModules as $_module) {
            $_module            = preg_replace('/\s+/', '', $_module);
            $_moduleArray      = explode('/', trim($_module, '/'));
            $_dbModuleName     = isset($_moduleArray[0]) ? $_moduleArray[0] : '';
            $_dbControllerName = isset($_moduleArray[1]) ? $_moduleArray[1] : 'index';
            $_dbActionName     = isset($_moduleArray[2]) ? $_moduleArray[2] : 'index';
            $this->log(
                '$_dbModuleName::' . $_dbModuleName . ', $_dbControllerName::' . $_dbControllerName . ', $_dbActionName::' . $_dbActionName
            );
            if ($_dbModuleName == $currentModuleName && ($_dbControllerName == $currentControllerName || $_dbControllerName == '*') && ($_dbActionName == $currentActionName || $_dbActionName == '*')) {
                $canAccess = true;
                break;
            }
        }

        return $canAccess;
    }

    public function isCustomerGroupAllowedForRestrictedStore()
    {
        $currentCustomerGroupId = $this->getCurrentCustomerGroupId();
        $allowedCustomerGroups  = $this->getConfig()->getRestrictedAllowedCustomerGroups();
        $this->log('$currentCustomerGroupId::' . $currentCustomerGroupId . ', $allowedCustomerGroups::' . implode(',', $allowedCustomerGroups));
        if (!empty($currentCustomerGroupId) && (in_array('-1', $allowedCustomerGroups) || in_array($currentCustomerGroupId, $allowedCustomerGroups))) {
            return true;
        } else {
            return false;
        }
    }

    public function getCurrentCustomerGroupId()
    {
        if (!Mage::helper('customer')->isLoggedIn()) {
            return 0;
        }
        $customer      = Mage::getSingleton('customer/session')->getCustomer();
        $customerGroup = $customer->getGroupId();
        return $customerGroup;
    }

    public function isLoginPage()
    {
        $loginPage     = $this->checkPageUrl('customer', 'account', 'login');
        $loginPostPage = $this->checkPageUrl('customer', 'account', 'loginPost');
        if ($loginPage || $loginPostPage) {
            return true;
        } else {
            return false;
        }
    }

    public function isLogoutPage()
    {
        $logoutPage     = $this->checkPageUrl('customer', 'account', 'logout');
        $logoutPostPage = $this->checkPageUrl('customer', 'account', 'logoutSuccess');
        if ($logoutPage || $logoutPostPage) {
            return true;
        } else {
            return false;
        }
    }

    public function isAccountCreatePage()
    {
        $createPage       = $this->checkPageUrl('customer', 'account', 'create');
        $createPostPage   = $this->checkPageUrl('customer', 'account', 'createpost');
        $confirmPage      = $this->checkPageUrl('customer', 'account', 'confirm');
        $confirmationPage = $this->checkPageUrl('customer', 'account', 'confirmation');
        if ($createPage || $createPostPage || $confirmPage || $confirmationPage) {
            // Tweak: if new account registration is disabled
            if ($this->isAccountRegistrationDisabled()) {
                return false;
            }

            return true;
        } else {
            return false;
        }
    }

    public function isForgotPasswordPage()
    {
        $forgotPage            = $this->checkPageUrl('customer', 'account', 'forgotpassword');
        $forgotPostPage        = $this->checkPageUrl('customer', 'account', 'forgotpasswordpost');
        $resetpasswordPage     = $this->checkPageUrl('customer', 'account', 'resetpassword');
        $resetpasswordPostPage = $this->checkPageUrl('customer', 'account', 'resetpasswordpost');
        $changeForgottenPage   = $this->checkPageUrl('customer', 'account', 'changeforgotten');

        if ($forgotPage || $forgotPostPage || $resetpasswordPage || $resetpasswordPostPage || $changeForgottenPage) {
            return true;
        } else {
            return false;
        }
    }

    public function is404ErrorPage()
    {
        $cmsIndexNoRoute            = $this->checkPageUrl('cms', 'index', 'noRoute');
        $cmsIndexDefaultNoRoute     = $this->checkPageUrl('cms', 'index', 'defaultNoRoute');
        if ($cmsIndexNoRoute || $cmsIndexDefaultNoRoute) {
            return true;
        } else {
            return false;
        }
    }

    public function isCookiePage()
    {
        if ($this->checkPageUrl('core', 'index', 'noCookies')) {
            return true;
        } else {
            return false;
        }
    }

    /******************************************************************************************************
     * STORE RESTRICTION - ACCESSIBLE / RESTRICTED
     *****************************************************************************************************/
    public function isRestrictionTypeAccessibleRestricted()
    {
        return $this->getConfig()->getRestrictionType() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Restrictiontypes::RESTRICTION_TYPE_ACCESSIBLE_RESTRICTED;
    }

    public function getAccessibleLandingPage()
    {
        $redirectionType = $this->getConfig()->getAccessibleRedirectionType();
        switch ($redirectionType) {
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CMS:
                //@todo check if the CMS page belongs to the store or not
                $cmsIdentifier = trim($this->getConfig()->getAccessibleRedirectionTypeCms(), '/');
                if ($cmsIdentifier == $this->_getHomepageIdentifier()) {
                    $cmsIdentifier = ''; //remove /home from url
                }
                $landingUrl = Mage::getUrl($cmsIdentifier);
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CUSTOM:
                $customPage = trim($this->getConfig()->getAccessibleRedirectionTypeCustom(), '/');
                $landingUrl = Mage::getUrl($customPage);
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_LOGIN:
            default:
                $landingUrl = Mage::getUrl('customer/account/login');
                break;
        }
        return $landingUrl;
    }

    public function getAllPriceBlocks()
    {
        return array(
            'Mage_Catalog_Block_Product_Price',
            'Mage_Bundle_Block_Catalog_Product_Price'
        );
    }

    public function isCustomerGroupAllowedForRestrictedArea()
    {
        $currentCustomerGroupId = $this->getCurrentCustomerGroupId();
        $allowedCustomerGroups  = $this->getConfig()->getAccessibleAllowedCustomerGroups();
        if (!empty($currentCustomerGroupId) && (in_array('-1', $allowedCustomerGroups) || in_array($currentCustomerGroupId, $allowedCustomerGroups))) {
            return true;
        } else {
            return false;
        }
    }

    public function isPriceSectionRestricted()
    {
        //check settings if enabled, check if current customer group is allowed or not
        $shouldHide = false;
        if ( !$this->isFxnSkipped() && $this->isRestrictionTypeAccessibleRestricted() && $this->getConfig()->getAccessibleHideProductPrices() && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $shouldHide = true;
        }
        return $shouldHide;
    }

    public function isAddToCartSectionRestricted()
    {
        //check settings if enabled, check if current customer group is allowed or not
        $shouldHide = false;
        if ( !$this->isFxnSkipped() && $this->isRestrictionTypeAccessibleRestricted() && $this->getConfig()->getAccessibleHideAddToCart() && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $shouldHide = true;
        }
        return $shouldHide;
    }

    public function isAccessibleCheckoutPageRestricted()
    {
        //check settings if enabled, check if current customer group is allowed or not
        $shouldRestrict = false;
        if ( !$this->isFxnSkipped() && $this->isRestrictionTypeAccessibleRestricted() && $this->getConfig()->getAccessibleHideCheckout() && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $shouldRestrict = true;
        }
        return $shouldRestrict;
    }

    public function skipPaymentMethodRestriction()
    {
        return $this->isFxnSkipped()
            || !$this->isRestrictionTypeAccessibleRestricted()
            || $this->isAdminArea()
            || !$this->hasRestrictedPaymentMethods()
            ;
    }

    public function hasRestrictedPaymentMethods()
    {
        $restrictedPaymentMethods    = $this->getConfig()->getAccessibleRestrictedPaymentMethods();
        $hasRestrictedPaymentMethods = true;
        if (
            ! count($restrictedPaymentMethods)
            || (count($restrictedPaymentMethods) == 1 && isset($restrictedPaymentMethods[0]) && $restrictedPaymentMethods[0] == '')
        ) {
            $hasRestrictedPaymentMethods = false;
        }
        return $hasRestrictedPaymentMethods;
    }

    public function isPaymentMethodSectionRestricted($paymentCode)
    {
        $shouldRestrict = false;
        if (in_array($paymentCode, $this->getConfig()->getAccessibleRestrictedPaymentMethods()) && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $shouldRestrict = true;
        }
        return $shouldRestrict;
    }

    public function skipShippingMethodRestriction()
    {
        return $this->isFxnSkipped()
               || !$this->isRestrictionTypeAccessibleRestricted()
               || $this->isAdminArea()
               || !$this->hasRestrictedShippingMethods()
            ;
    }

    public function hasRestrictedShippingMethods()
    {
        $restrictedShippingMethods    = $this->getConfig()->getAccessibleRestrictedShipmentMethods();
        $hasRestrictedShippingMethods = true;
        if (
            ! count($restrictedShippingMethods)
            || (count($restrictedShippingMethods) == 1 && isset($restrictedShippingMethods[0]) && $restrictedShippingMethods[0] == '')
        ) {
            $hasRestrictedShippingMethods = false;
        }
        return $hasRestrictedShippingMethods;
    }

    public function isShippingMethodSectionRestricted($shippingCode)
    {
        $shouldRestrict = false;
        if (in_array($shippingCode, $this->getConfig()->getAccessibleRestrictedShipmentMethods()) && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $shouldRestrict = true;
        }
        $this->log('$shippingCode::' . $shippingCode . ', $shouldRestrict::' . (int)$shouldRestrict);
        return $shouldRestrict;
    }

    public function isAccessibleCmsPageRestricted()
    {
        $isRestricted = false;
        $currentIdentifier = '';
        if ($this->_isHomepage()) {
            $currentIdentifier = $this->_getHomepageIdentifier();
            $this->log('Homepage::$currentIdentifier::' . $currentIdentifier);
        } else {
            $request =  Mage::app()->getRequest();
            $pageId  = $request->getParam('page_id', $request->getParam('id', false));
            if ($pageId) {
                $page = Mage::getModel('cms/page')->load($pageId);
                $currentIdentifier = $page->getIdentifier();
            }
            $this->log('Other CMS::$currentIdentifier::' . $currentIdentifier);
        }

        $restrictedCmsPages   = $this->getConfig()->getAccessibleRestrictedCmsPages();
        $this->log('$restrictedCmsPages::' . implode(', ', $restrictedCmsPages));

        if (!empty($currentIdentifier) && in_array($currentIdentifier, $restrictedCmsPages) && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $isRestricted = true;
        }

        if ($this->getConfig()->getAccessibleRedirectionType() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes::REDIRECTION_TYPE_CMS) {
            if ($currentIdentifier == $this->getConfig()->getAccessibleRedirectionTypeCms()) {
                $isRestricted = false;
            }
        }
        return $isRestricted;
    }

    public function isAccessibleCategoryPageRestricted()
    {
        $isRestricted   = false;
        $request        = Mage::app()->getRequest();
        $currentCategoryId = $request->getParam('id');
        $this->log('$currentCategoryId::' . $currentCategoryId);
        $restrictedCategories = $this->getConfig()->getAccessibleRestrictedCategoryPages();
        $this->log('$restrictedCategories::' . implode(', ', $restrictedCategories));
        if (in_array($currentCategoryId, $restrictedCategories) && ! $this->isCustomerGroupAllowedForRestrictedArea()) {
            $isRestricted = true;
        }
        return $isRestricted;
    }

    public function isAccessibleProductPageRestricted()
    {
        $isRestricted       = false;
        $request            = Mage::app()->getRequest();
        $currentProductId   = $request->getParam('id');
        $currentProductSku  = Mage::getModel('catalog/product')->load($currentProductId)->getSku();
        $this->log('$currentProductSku::' . $currentProductSku);
        $restrictedProducts    = $this->getConfig()->getAccessibleRestrictedProductPages();
        $this->log('$restrictedProducts::' . implode(',', $restrictedProducts));
        if (in_array($currentProductSku, $restrictedProducts) && !$this->isCustomerGroupAllowedForRestrictedArea()) {
            $isRestricted = true;
        }
        return $isRestricted;
    }

    public function isAccessibleModulePageRestricted()
    {
        $isRestricted          = false;
        $request               = Mage::app()->getRequest();
        $currentModuleName     = $request->getModuleName();
        $currentControllerName = $request->getControllerName();
        $currentActionName     = $request->getActionName();

        $this->log(
            '$currentModuleName::' . $currentModuleName . ', $currentControllerName::' . $currentControllerName . ', $currentActionName::' . $currentActionName
        );

        $restrictedModules = $this->getConfig()->getAccessibleRestrictedModulePages();
        $this->log('$restrictedModules::' . implode(', ', $restrictedModules));
        foreach ($restrictedModules as $_module) {
            $_module            = preg_replace('/\s+/', '', $_module);
            $_moduleArray      = explode('/', trim($_module, '/'));
            $_dbModuleName     = isset($_moduleArray[0]) ? $_moduleArray[0] : '';
            $_dbControllerName = isset($_moduleArray[1]) ? $_moduleArray[1] : 'index';
            $_dbActionName     = isset($_moduleArray[2]) ? $_moduleArray[2] : 'index';
            $this->log(
                '$_dbModuleName::' . $_dbModuleName . ', $_dbControllerName::' . $_dbControllerName . ', $_dbActionName::' . $_dbActionName
            );
            if ($_dbModuleName == $currentModuleName && ($_dbControllerName == $currentControllerName || $_dbControllerName == '*') && ($_dbActionName == $currentActionName || $_dbActionName == '*') && !$this->isCustomerGroupAllowedForRestrictedArea()) {
                $isRestricted = true;
                break;
            }
        }

        return $isRestricted;
    }
}