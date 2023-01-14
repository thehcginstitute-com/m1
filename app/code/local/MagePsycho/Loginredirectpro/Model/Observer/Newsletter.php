<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Model_Observer_Newsletter
{
	protected $_helper;

	public function __construct()
	{
		$this->_helper = Mage::helper('magepsycho_loginredirectpro');
	}

	/**
	 * Redirect to custom page after newsletter subscription
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerActionPostdispatchNewsletterSubscriber(Varien_Event_Observer $observer)
	{
		if ($this->_helper->isFxnSkipped()) {
			return;
		}

		$action = $observer->getEvent()->getControllerAction();

		$redirectUrl = $this->_helper->getNewsletterRedirectionUrl();
		if (empty($redirectUrl)) {
			return;
		}

		$action->getResponse()->setRedirect($redirectUrl);
		$action->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, TRUE);
	}
}