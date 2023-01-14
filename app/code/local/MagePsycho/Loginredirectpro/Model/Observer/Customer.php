<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Model_Observer_Customer
{
	protected $_helper;

	public function __construct()
	{
		$this->_helper = Mage::helper('magepsycho_loginredirectpro');
	}

	/**
	 * Retrieve customer session model object
	 *
	 * @return Mage_Customer_Model_Session
	 */
	protected function _getSession()
	{
		return Mage::getSingleton('customer/session');
	}

	/**
	 * @return Mage_Core_Model_Session
	 */
	protected function _getCoreSession()
	{
		return Mage::getSingleton('core/session');
	}

	/**
	 * Prepare redirection after customer registration
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function customerRegisterSuccess(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped()) {
			return;
		}

		$customer = $observer->getEvent()->getCustomer();
		$groupId  = $customer->getGroupId();
		$successUrl = $this->_helper->getAccountRedirectionUrl($groupId);
		$this->_getSession()->setBeforeAuthUrl($successUrl);
		#$this->_getSession()->setCustomerRegisterSuccessRedirect(true);
		$customer->setCustomerRegisterSuccessRedirect(true);
	}

	/**
	 * Prepare custom success message after registration
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerActionPostdispatchCustomerAccountCreatePost(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped()) {
			return;
		}

		$successMessage = $this->_helper->getAccountSuccessMessage();
		if ( !empty($successMessage)) {
			// @todo note it's consequence
			$this->_getSession()->getMessages(true);
			$this->_getSession()->addSuccess(
				sprintf($successMessage, Mage::app()->getStore()->getFrontendName())
			);
		}
	}

	/**
	 * Prepare redirection after customer login
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function customerLogin(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped()) {
			return;
		}

		$customer = $observer->getEvent()->getCustomer();

		if (strpos($this->_helper->getRefererUrl(), '/checkout/onepage') === false && ! $customer->getCustomerRegisterSuccessRedirect()) {
			if ($this->_getSession()->isLoggedIn()) {
				$redirectionUrl = $this->_helper->getLoginRedirectionUrl();
				$this->_getSession()->setBeforeAuthUrl($redirectionUrl);
			} else {
				$this->_getSession()->setBeforeAuthUrl(Mage::helper('customer')->getLoginUrl());
			}

			$logoutRedirectionUrl = $this->_helper->getLogoutRedirectionUrl();
			$this->_getCoreSession()->setAfterLogoutUrlClrp($logoutRedirectionUrl);
			$customer->setCustomerRegisterSuccessRedirect(false);
		}
	}

	/**
	 * Prepare redirection after customer logout
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerActionPostdispatchCustomerAccountLogout(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped() || !$this->_helper->getConfig()->getRemoveLogoutIntermediate()) {
			return;
		}

		$logoutRedirectionUrl = $this->_helper->getLogoutRedirectionUrl();

		// Extract url path
		if ( !empty($logoutRedirectionUrl)) {
			$baseUrl                 = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, true);
			$logoutRedirectionPath   = str_replace(trim($baseUrl, '/'), '', $logoutRedirectionUrl);
			$logoutRedirectionPath   = trim($logoutRedirectionPath, '/');
		} else {
			$logoutRedirectionPath = '/';
		}
		$this->_helper->log('$logoutRedirectionPath::' . $logoutRedirectionPath);
		$controller = $observer->getEvent()->getControllerAction();
		$controller->setRedirectWithCookieCheck($logoutRedirectionPath);
	}

	/**
	 * Dynamic Rewrite of Customer Model class
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerFrontInitBefore(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped() || $this->_helper->isAccountGroupTemplateEmpty()) {
			return;
		}

		Mage::getConfig()->setNode('global/models/customer/rewrite/customer', 'MagePsycho_Loginredirectpro_Model_Rewrite_Customer');
	}

}