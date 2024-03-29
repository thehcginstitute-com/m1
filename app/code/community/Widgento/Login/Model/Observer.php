<?php
class Widgento_Login_Model_Observer
{
	private static $availableActionNames = array(
		'widgentologin_index_index',
	);

	/**
	 * @event websiterestriction_frontend
	 * @param Varien_Event_Observer $observer
	 */
	function removeFrontendRestrictions(Varien_Event_Observer $observer)
	{
		/* @var $controller Mage_Core_Controller_Varien_Action */
		$controller = $observer->getController();
		$result     = $observer->getResult();

		if (in_array($controller->getFullActionName(), self::$availableActionNames)) {
			$result->setShouldProceed(false);
		}
	}
}