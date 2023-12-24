<?php
use Varien_Event_Observer as O;
# 2023-12-24
final class HCG_Core_Observer {
	/**
	 * 2023-12-24
	 * "The Magento's home page should redirect the visitor to the «Products» page":
	 * https://github.com/thehcginstitute-com/m1/issues/73
	 * @used-by Mage_Core_Model_App::_callObserverMethod()
	 */
	function controller_action_predispatch_cms_index_index(O $o):void {
		Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getUrl('products'));
	}
}