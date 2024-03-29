<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Model_Observer
{
	/**
	 * Hide License key fields if the extension comes in a bundle
	 *
	 * @param Varien_Event_Observer $observer
	 */
	function adminhtmlInitSystemConfig(Varien_Event_Observer $observer)
	{
		$helper             = hcg_mp_hc();
		$config             = $observer->getEvent()->getConfig();
		$fields             = $config->getNode('sections/magepsycho_customerregfields/groups/option/fields');
		if (!empty($fields) && $helper->hasBundleExtensions()) {
			if (property_exists($fields, 'domain')) {
				$fields->domain->show_in_default = 0;
				$fields->domain->show_in_website = 0;
				$fields->domain->show_in_store   = 0;
			}
			if (property_exists($fields, 'domain_type')) {
				$fields->domain_type->show_in_default = 0;
				$fields->domain_type->show_in_website = 0;
				$fields->domain_type->show_in_store   = 0;
			}
			if (property_exists($fields, 'dev_license')) {
				$fields->dev_license->show_in_default = 0;
				$fields->dev_license->show_in_website = 0;
				$fields->dev_license->show_in_store   = 0;
			}
			if (property_exists($fields, 'prod_license')) {
				$fields->prod_license->show_in_default = 0;
				$fields->prod_license->show_in_website = 0;
				$fields->prod_license->show_in_store   = 0;
			}
		}
	}

	/**
	 * Observes the event to set Group ID as per group code
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 * @return $this
	 */
	function customerSaveBefore(Varien_Event_Observer $observer)
	{
		$helper = hcg_mp_hc();
		if ($helper->skipGroupCodeSelectorFxn()) {
			return $this;
		}

		try {
			$customer       = $observer->getEvent()->getCustomer();
			$groupCode      = $customer->getMpGroupCode();
			if (empty($groupCode)) {
				return $this;
			}

			if ($groupId = $helper->checkIfGroupCodeIsValid($groupCode)) {
				$customer->setGroupId($groupId);
				$customer->setMpGroupCode($groupCode);
			}

		} catch (Exception $e) {
			Mage::logException($e);
		}
	}

	/**
	 * Capture group id/code from billing address form
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 * @return $this
	 */
	function controllerActionPostdispatchCheckoutOnepageSaveBilling(Varien_Event_Observer $observer)
	{
		$helper     = hcg_mp_hc();
		if (!$helper->enabled() || Mage::getSingleton('customer/session')->isLoggedIn()) {
			return $this;
		}
		
		$event          = $observer->getEvent();
		$request        = $event->getControllerAction()->getRequest();

		$billingData    = $request->getParam('billing');

		// capture group id/code value and set it to session
		$groupId        = isset($billingData['group_id']) ? $billingData['group_id'] : null;
		$groupCode      = isset($billingData['mp_group_code']) ? $billingData['mp_group_code'] : '';

		Mage::getSingleton('checkout/session')->setSessMpGroupId($groupId);
		Mage::getSingleton('checkout/session')->setSessMpGroupCode($groupCode);
	}

	/**
	 * Inject captured group id/code to $customer
	 * Doesn't work for $order
	 * @see salesOrderSaveAfter()
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 * @return $this
	 */
	function checkoutTypeOnepageSaveOrder(Varien_Event_Observer $observer)
	{
		$helper     = hcg_mp_hc();
		$event      = $observer->getEvent();
		$quote      = $event->getQuote();
		$order      = $event->getOrder();

		if (!$helper->enabled()
		     || Mage::getSingleton('customer/session')->isLoggedIn()
			 || $quote->getData('checkout_method') != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER
		) {
			return $this;
		}
		// Set $customer object and customerSaveBefore() will take care of the rest
		$customer       = $quote->getCustomer();
		if ($groupId = Mage::getSingleton('checkout/session')->getSessMpGroupId()) {
			$customer->setGroupId($groupId);
		}
		if ($groupCode = Mage::getSingleton('checkout/session')->getSessMpGroupCode()) {
			$customer->setMpGroupCode($groupCode);
		}
	}

	/**
	 * Update customer_group_id to $order
	 *
	 * @param Varien_Event_Observer $observer
	 *
	 * @return $this
	 */
	function salesOrderSaveAfter(Varien_Event_Observer $observer)
	{
		$helper     = hcg_mp_hc();
		$event      = $observer->getEvent();
		$order      = $event->getOrder();

		if (!$helper->enabled() || Mage::getSingleton('customer/session')->isLoggedIn()) {
			return $this;
		}

		if ($groupId = Mage::getSingleton('checkout/session')->getSessMpGroupId()) {
			$order->setCustomerGroupId($groupId);
			Mage::getSingleton('checkout/session')->setSessMpGroupId();
		}
		if ($groupCode = Mage::getSingleton('checkout/session')->getSessMpGroupCode()) {
			if ($groupId = $helper->checkIfGroupCodeIsValid($groupCode)) {
				$order->setCustomerGroupId($groupId);
				Mage::getSingleton('checkout/session')->setSessMpGroupCode();
			}
		}
	}

}