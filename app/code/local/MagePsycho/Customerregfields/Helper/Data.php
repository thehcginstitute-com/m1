<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MODULE_NAMESPACE_ALIAS = 'magepsycho_customerregfields';

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
     * @return MagePsycho_Customerregfields_Helper_Config
     */
    public function getConfig()
    {
        return Mage::helper('magepsycho_customerregfields/config');
    }

    /**
     * Module Logging function
     *
     * @param $data
     * @param bool|false $includeSep
     */
    public function log($data, $includeSep = false)
    {
        if (!$this->getConfig()->isLogEnabled()) {
            return;
        }
        if ($includeSep) {
            $separator = str_repeat('=', 70);
            Mage::log($separator, null, self::MODULE_NAMESPACE_ALIAS . '.log', true);
        }
        Mage::log($data, null, self::MODULE_NAMESPACE_ALIAS . '.log', true);
    }

    /**
     * Check version
     *
     * @param $version
     * @param string $operator
     *
     * @return mixed
     */
    public function checkVersion($version, $operator = '>=')
    {
        return version_compare(Mage::getVersion(), $version, $operator);
    }

    /**
     * Get extension version
     *
     * @return string
     */
    public function getExtensionVersion()
    {
        $moduleCode = 'MagePsycho_Customerregfields';
        return (string)$currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
    }

    /**
     * Get Store wise domain for System > Configuraiton settings
     *
     * @return string
     */
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
        $message = base64_decode('WW91IGFyZSB1c2luZyB1bmxpY2Vuc2VkIHZlcnNpb24gb2YgJ0N1c3RvbWVyIEdyb3VwIFNlbGVjdG9yJyBFeHRlbnNpb24gZm9yIGRvbWFpbjoge3tET01BSU59fS4gUGxlYXNlIGVudGVyIGEgdmFsaWQgTGljZW5zZSBLZXkgZnJvbSBTeXN0ZW0gJnJhcXVvOyBDb25maWd1cmF0aW9uICZyYXF1bzsgTWFnZVBzeWNobyBFeHRlbnNpb25zICZyYXF1bzsgQ3VzdG9tZXIgR3JvdXAgU2VsZWN0b3IgJnJhcXVvOyBMaWNlbnNlIEtleS4gSWYgeW91IGRvbid0IGhhdmUgb25lLCBwbGVhc2UgcHVyY2hhc2UgYSB2YWxpZCBsaWNlbnNlIGZyb20gPGEgaHJlZj0iaHR0cDovL3d3dy5tYWdlcHN5Y2hvLmNvbS9jb250YWN0cyIgdGFyZ2V0PSJfYmxhbmsiPnd3dy5tYWdlcHN5Y2hvLmNvbTwvYT4gb3IgeW91IGNhbiBkaXJlY3RseSBlbWFpbCB0byA8YSBocmVmPSJtYWlsdG86aW5mb0BtYWdlcHN5Y2hvLmNvbSI+aW5mb0BtYWdlcHN5Y2hvLmNvbTwvYT4=');
        $message = str_replace('{{DOMAIN}}', $this->getDomain(), $message);
        return $message;
    }

    public function getDomain()
    {
        $domain     = Mage::getBaseUrl();
        $baseDomain = Mage::helper('magepsycho_customerregfields/url')->getBaseDomain($domain);
        return strtolower($baseDomain);
    }

    public function checkEntry($domain, $serial)
    {
        $salt = sha1(base64_decode('Z3JvdXBzZWxlY3Rvcg=='));
        if (sha1($salt . $domain . $this->mode) == $serial) {
            return true;
        }
        return false;
    }

    public function isValid()
    {
        $temp = $this->temp;
        if ($this->checkEntry($this->getDomain(), $temp)) {
            return true;
        } else {
            if ($this->hasBundleExtensions()) {
                return true;
            }
            return false;
        }
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
     * Checks if the extension is bundled with others
     *
     * @return bool
     */
    public function hasBundleExtensions()
    {
        if (Mage::helper('core')->isModuleEnabled('MagePsycho_Loginredirectpro') ||
        Mage::helper('core')->isModuleEnabled('MagePsycho_Storerestrictionpro')) {
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
        $request = Mage::app()->getRequest();
        $isApiRequest = ($request->getModuleName() === 'api' || $request->getModuleName() === 'oauth') ? true : false;
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

    /**
     * Switch customer registration template
     *
     * @return string
     */
    public function switchCustomerFormRegisterTemplateIf()
    {
        if ( ! $this->isFxnSkipped()) {
            return 'magepsycho/customerregfields/customer/form/register.phtml';
        } else {
            return 'persistent/customer/form/register.phtml';
        }
    }

    /**
     * Switch customer edit template
     *
     * @return string
     */
    public function switchCustomerAccountEditTemplateIf()
    {
        if ( ! $this->isFxnSkipped() /*&& $this->getConfig()->isGroupSelectionEditable()*/) {
            return 'magepsycho/customerregfields/customer/form/edit.phtml';
        } else {
            return 'customer/form/edit.phtml';
        }
    }

    /**
     * Switch checkout onepage billing form template
     *
     * @return string
     */
    public function switchCheckoutOnepageBillingTemplateIf()
    {
        if ( ! $this->isFxnSkipped() &&
             $this->getConfig()->isEnabledForCheckout() &&
             ! Mage::getSingleton('customer/session')->isLoggedIn()
        ) {
            return 'magepsycho/customerregfields/checkout/onepage/billing.phtml';
        } else {
            return 'checkout/onepage/billing.phtml';
        }
    }

    /**
     * Check if request is valid for group code
     *
     * @return bool
     */
    public function skipGroupCodeSelectorFxn()
    {
        return !$this->getConfig()->isActive()
               || $this->getConfig()->getGroupSelectionType() != MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_GROUP_CODE
               || $this->isAdminArea()
               || $this->isApiRequest();
    }

    /**
     * Load customer groups
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

    /**
     * Generate array of group/code from system configuration
     *
     * @return array
     */
    protected function _getDbGroupCodes()
    {
        $groupCodeData = $this->getConfig()->getGroupCodeData();
        $groupCodes    = array();
        if ( !empty($groupCodeData)) {
            $groupCodesArray = unserialize($groupCodeData);
            if ( !empty($groupCodesArray)) {
                foreach ($groupCodesArray as $_data) {
                    $groupId    = isset($_data['customer_group_id']) ? $_data['customer_group_id'] : null;
                    $groupCode  = isset($_data['group_code']) ? $_data['group_code'] : null;
                    if (!empty($groupId) && !empty($groupCode)) {
                        $groupCodes[$groupId] = $groupCode;
                    }
                }
            }
        }
        return $groupCodes;
    }

    /**
     * Get final array of group codes based on db settings
     *
     * @param bool|false $onlyNonEmpty
     *
     * @return array
     */
    public function getGroupCodes($onlyNonEmpty = false)
    {
        $groups         = $this->getCustomerGroups();
        $groupCodes     = array();
        $_dbGroupCodes  = $this->_getDbGroupCodes();
        foreach ($groups as $group) {

            $groupId    = $group['value'];
            $groupCode  = isset($_dbGroupCodes[$groupId]) ? $_dbGroupCodes[$groupId] : '';
            if ($onlyNonEmpty) {
                if ( ! empty($groupCode)) {
                    $groupCode  = preg_replace('/\s*,\s*/', ',', trim($groupCode));
                    $codes      = explode(',', $groupCode);
                    $groupCodes[$groupId] = $codes;
                }
            } else {
                $codes = array();
                if ( ! empty($groupCode)) {
                    $groupCode  = preg_replace('/\s*,\s*/', ',', trim($groupCode));
                    $codes      = explode(',', $groupCode);
                }
                $groupCodes[$groupId] = $codes;
            }
        }
        return $groupCodes;
    }

    /**
     * Checks & returns Group Id from Valid Code
     *
     * @param $groupCode
     *
     * @return mixed
     */
    public function checkIfGroupCodeIsValid($groupCode)
    {
        $groupCodesArray    = $this->getGroupCodes();
        $matchedGroupId     = false;
        foreach ($groupCodesArray as $groupId => $groupCodes) {
            $matchGroupCodes = $groupCodes;
            if (!is_array($groupCodes)) {
                $matchGroupCodes = array($groupCodes);
            }

            if (!empty($groupCode) && in_array($groupCode, $matchGroupCodes)) {
                $matchedGroupId = $groupId;
                break;
            }
        }
        return $matchedGroupId;
    }

    /**
     * Check if customer is valid for editing the group
     *
     * @return bool
     */
    public function isValidCustomerForEdit()
    {
        $allowEdit            = false;
        $loggedInCustomer = Mage::helper('customer')->getCustomer();
        if ($loggedInCustomer) {
            $groupSelectorType          = $this->getConfig()->getGroupSelectionType();
            $loggedInCustomerGroupId    = $loggedInCustomer->getGroupId();
            if ($groupSelectorType == MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_GROUP_CODE) {
                $groupCodes             = $this->getGroupCodes(true);
                $allowedCustomerGroup   = is_array($groupCodes) ? array_keys($groupCodes) : array();
                if ( ! empty($allowedCustomerGroup) && in_array($loggedInCustomerGroupId, $allowedCustomerGroup)) {
                    $allowEdit = true;
                }
            } elseif ($groupSelectorType == MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_DROPDOWN) {
                $allowedCustomerGroup = $this->getConfig()->getAllowedCustomerGroups();
                $dbGroups             = explode(',', $allowedCustomerGroup);
                if (in_array('-1', $dbGroups) || in_array($loggedInCustomerGroupId, $dbGroups)) {
                    $allowEdit = true;
                }
            }

        }
        return $allowEdit;
    }

    /**
     * Prepare group code for table array field of system configuration
     *
     * @return array|mixed
     */
    public function getGroupSelectOptions()
    {
        $allowedCustomerGroup = $this->getConfig()->getAllowedCustomerGroups();
        $dbGroups             = explode(',', $allowedCustomerGroup);
        if (in_array('-1', $dbGroups)) {
            $groupOptions = $this->getCustomerGroups();
        } else {
            $groupOptions = array();
            foreach ($dbGroups as $_groupId) {
                $data           = Mage::getModel('customer/group')->load($_groupId);
                $label          = $data['customer_group_code'];
                $groupOptions[] = array(
                    'value' => $_groupId,
                    'label' => $label,
                );
            }
        }
        array_unshift($groupOptions, array('label' => '', 'value' => ''));
        return $groupOptions;
    }
}