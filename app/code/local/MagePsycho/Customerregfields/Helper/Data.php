<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * 2024-01-27 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
 * @see MagePsycho_Customerregfields_Helper_Config
 */
class MagePsycho_Customerregfields_Helper_Data extends HCG\MagePsycho\Helper
{
	/**
	 * Module Logging function
	 *
	 * @param $data
	 * @param bool|false $includeSep
	 */
	function log($data, $includeSep = false)
	{
		if (!$this->cfgH()->isLogEnabled()) {
			return;
		}
		if ($includeSep) {
			$separator = str_repeat('=', 70);
			Mage::log($separator, null, $this->moduleMf() . '.log', true);
		}
		Mage::log($data, null, $this->moduleMf() . '.log', true);
	}

	/**
	 * Check version
	 *
	 * @param $version
	 * @param string $operator
	 *
	 * @return mixed
	 */
	function checkVersion($version, $operator = '>=')
	{
		return version_compare(Mage::getVersion(), $version, $operator);
	}

	/**
	 * Get extension version
	 *
	 * @return string
	 */
	function getExtensionVersion()
	{
		$moduleCode = 'MagePsycho_Customerregfields';
		return (string)$currentVer = Mage::getConfig()->getModuleConfig($moduleCode)->version;
	}

	/**
	 * Get Store wise domain for System > Configuraiton settings
	 *
	 * @return string
	 */
	function getDomainFromSystemConfig()
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

	/**
	 * Checks if the extension is bundled with others
	 * @used-by \MagePsycho_Customerregfields_Model_Observer::adminhtmlInitSystemConfig()
	 * @return bool
	 */
	function hasBundleExtensions()
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
	function isApiRequest()
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
	function isAdminArea()
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
	function switchCustomerFormRegisterTemplateIf()
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
	function switchCustomerAccountEditTemplateIf()
	{
		if ( ! $this->isFxnSkipped() /*&& $this->cfgH()->isGroupSelectionEditable()*/) {
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
	function switchCheckoutOnepageBillingTemplateIf()
	{
		if ( ! $this->isFxnSkipped() &&
			 $this->cfgH()->isEnabledForCheckout() &&
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
	function skipGroupCodeSelectorFxn()
	{
		return !$this->cfgH()->isActive()
			   || $this->cfgH()->getGroupSelectionType() != MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_GROUP_CODE
			   || $this->isAdminArea()
			   || $this->isApiRequest();
	}

	/**
	 * Load customer groups
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

	/**
	 * Generate array of group/code from system configuration
	 *
	 * @return array
	 */
	protected function _getDbGroupCodes()
	{
		$groupCodeData = $this->cfgH()->getGroupCodeData();
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
	function getGroupCodes($onlyNonEmpty = false)
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
	function checkIfGroupCodeIsValid($groupCode)
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
	function isValidCustomerForEdit()
	{
		$allowEdit            = false;
		$loggedInCustomer = Mage::helper('customer')->getCustomer();
		if ($loggedInCustomer) {
			$groupSelectorType          = $this->cfgH()->getGroupSelectionType();
			$loggedInCustomerGroupId    = $loggedInCustomer->getGroupId();
			if ($groupSelectorType == MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_GROUP_CODE) {
				$groupCodes             = $this->getGroupCodes(true);
				$allowedCustomerGroup   = is_array($groupCodes) ? array_keys($groupCodes) : array();
				if ( ! empty($allowedCustomerGroup) && in_array($loggedInCustomerGroupId, $allowedCustomerGroup)) {
					$allowEdit = true;
				}
			} elseif ($groupSelectorType == MagePsycho_Customerregfields_Model_System_Config_Source_Selectortypes::SELECTOR_TYPE_DROPDOWN) {
				$allowedCustomerGroup = $this->cfgH()->getAllowedCustomerGroups();
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
	function getGroupSelectOptions()
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

	/**
	 * 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
	 * @override
	 * @see HCG\MagePsycho\Helper::moduleMf()
	 * @used-by HCG\MagePsycho\Helper::cfg()
	 * @used-by HCG\MagePsycho\Helper::cfgH()
	 * @used-by self::log()
	 */
	final protected function moduleMf():string {return 'magepsycho_customerregfields';}
}