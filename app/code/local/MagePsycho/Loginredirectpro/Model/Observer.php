<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Model_Observer
{
	protected $_helper;

	public function __construct()
	{
		$this->_helper = Mage::helper('magepsycho_loginredirectpro');
	}

	protected function _getCustomerSession()
	{
		return Mage::getSingleton('customer/session');
	}

	protected function _getAdminSession()
	{
		return Mage::getSingleton('adminhtml/session');
	}

	protected function _getRequest()
	{
		return Mage::app()->getRequest();
	}

	public function controllerActionPredispatch(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped() ||
			$this->_helper->isAdminArea() ||
		    $this->_helper->isApiRequest()
		) {
			return;
		}

		$moduleName		= $this->_getRequest()->getModuleName();
		$controllerName	= $this->_getRequest()->getControllerName();
		$fullActionName = $observer->getControllerAction()->getFullActionName();

		if ($moduleName != 'customer' &&
		    $controllerName != 'account'
		) {
			if ( !in_array($fullActionName, array('cms_index_noRoute', 'cms_index_defaultNoRoute'))
			    && !$this->_getRequest()->isXmlHttpRequest()
			) {
				$currentUrl = Mage::helper("core/url")->getCurrentUrl();
				$this->_getCustomerSession()->setBeforeAuthUrlClrp($currentUrl);
				$this->_helper->log('setBeforeAuthUrlClrp::'.$currentUrl);
			}
		}

		if ($redirectToParamUrl = $this->_helper->getRedirectToParamUrl()) {
			$this->_getCustomerSession()->setRedirectToUrlClrp($redirectToParamUrl);
			$this->_helper->log('setRedirectToUrlClrp::' . $redirectToParamUrl);
		}

        return $this;
	}

    public function adminhtmlPreDispatchCheck(Varien_Event_Observer $observer)
    {
        $fullActionName		= $observer->getEvent()->getControllerAction()->getFullActionName();
        if ($this->_helper->isActive() &&
            !$this->_helper->isValid() &&
            'adminhtml_system_config_edit' == $fullActionName
        ) {
            $this->_getAdminSession()->getMessages(true);
            $this->_getAdminSession()->addError($this->_helper->getMessage());
            return;
        }
        return $this;
    }
}