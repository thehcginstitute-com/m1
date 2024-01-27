<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Block_Customer_Logout extends Mage_Core_Block_Template
{
	protected function _getHelper()
	{
		return Mage::helper('magepsycho_loginredirectpro');
	}

	protected function _getCoreSession()
	{
		return Mage::getSingleton('core/session');
	}

	function getRedirectionUrl()
	{
		$redirectionUrl = $this->_getCoreSession()->getAfterLogoutUrlClrp();
		if (empty($redirectionUrl)) {
			$redirectionUrl = $this->_getHelper()->cfgH()->getDefaultLogoutUrl();
		}

		return $redirectionUrl;
	}

	function getDelayTime($convert = false)
	{
		$delayTime = $this->_getHelper()->cfgH()->getLogoutDelay();
		if ($convert) {
			$delayTime = (int) $delayTime * 1000;;
		}
		return $delayTime;
	}

	function getCustomMessage()
	{
		return $this->_getHelper()->cfgH()->getLogoutMessage();
	}
}