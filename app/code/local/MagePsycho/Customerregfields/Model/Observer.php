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
	public function adminhtmlControllerActionPredispatch(Varien_Event_Observer $observer)
	{
		$helper           = Mage::helper('magepsycho_customerregfields');
		$isValid          = $helper->isValid();
		$isActive         = $helper->isActive();
		$adminhtmlSession = Mage::getSingleton('adminhtml/session');
		$fullActionName   = $observer->getEvent()->getControllerAction()->getFullActionName();
		if ($isActive && !$isValid && 'adminhtml_system_config_edit' == $fullActionName) {
			$adminhtmlSession->getMessages(true);
			$adminhtmlSession->addError($helper->getMessage());
			return $this;
		}

		return $this;
	}

	/**
	 * Hide License key fields if the extension comes in a bundle
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function adminhtmlInitSystemConfig(Varien_Event_Observer $observer)
	{
		$helper             = Mage::helper('magepsycho_customerregfields');
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
	public function customerSaveBefore(Varien_Event_Observer $observer)
	{
		$helper     = Mage::helper('magepsycho_customerregfields');
		$helper->log(__METHOD__, true);
		if ($helper->skipGroupCodeSelectorFxn()) {
			$helper->log('SKIPPED::NotValidRequestForGroupCode');
			return $this;
		}

		try {
			$customer       = $observer->getEvent()->getCustomer();
			$groupCode      = $customer->getMpGroupCode();
			$helper->log('$groupCode::' . $groupCode);
			if (empty($groupCode)) {
				$helper->log('SKIPPED::EmptyGroupCode');
				return $this;
			}

			if ($groupId = $helper->checkIfGroupCodeIsValid($groupCode)) {
				$customer->setGroupId($groupId);
				$customer->setMpGroupCode($groupCode);
				$helper->log('CustomerGroupSet', true);
				$helper->log('customerEmail::' . $customer->getEmail());
				$helper->log('setGroupId::' . $groupId);
				$helper->log('setGroupCode::' . $groupCode);
			}

		} catch (Exception $e) {
			$helper->log('Exception::' . $e->getMessage());
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
	public function controllerActionPostdispatchCheckoutOnepageSaveBilling(Varien_Event_Observer $observer)
	{
		$helper     = Mage::helper('magepsycho_customerregfields');
		if ( $helper->isFxnSkipped() ||
		     Mage::getSingleton('customer/session')->isLoggedIn()
		) {
			return $this;
		}

		$helper->log(__METHOD__, true);
		$event          = $observer->getEvent();
		$request        = $event->getControllerAction()->getRequest();

		$billingData    = $request->getParam('billing');

		// capture group id/code value and set it to session
		$groupId        = isset($billingData['group_id']) ? $billingData['group_id'] : null;
		$groupCode      = isset($billingData['mp_group_code']) ? $billingData['mp_group_code'] : '';

		$helper->log('$groupId::' . $groupId);
		$helper->log('$groupCode::' . $groupCode);
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
	public function checkoutTypeOnepageSaveOrder(Varien_Event_Observer $observer)
	{
		$helper     = Mage::helper('magepsycho_customerregfields');
		$event      = $observer->getEvent();
		$quote      = $event->getQuote();
		$order      = $event->getOrder();

		if ( $helper->isFxnSkipped()
		     || Mage::getSingleton('customer/session')->isLoggedIn()
			 || $quote->getData('checkout_method') != Mage_Checkout_Model_Type_Onepage::METHOD_REGISTER
		) {
			return $this;
		}

		$helper->log(__METHOD__, true);

		// Set $customer object and customerSaveBefore() will take care of the rest
		$customer       = $quote->getCustomer();
		if ($groupId = Mage::getSingleton('checkout/session')->getSessMpGroupId()) {
			$helper->log('$groupId::' . $groupId);
			$customer->setGroupId($groupId);
		}
		if ($groupCode = Mage::getSingleton('checkout/session')->getSessMpGroupCode()) {
			$helper->log('$groupCode::' . $groupCode);
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
	public function salesOrderSaveAfter(Varien_Event_Observer $observer)
	{
		$helper     = Mage::helper('magepsycho_customerregfields');
		$event      = $observer->getEvent();
		$order      = $event->getOrder();

		if ( $helper->isFxnSkipped()
		     || Mage::getSingleton('customer/session')->isLoggedIn()
		) {
			return $this;
		}

		$helper->log(__METHOD__, true);

		if ($groupId = Mage::getSingleton('checkout/session')->getSessMpGroupId()) {
			$helper->log('$groupId::' . $groupId);
			$order->setCustomerGroupId($groupId);
			Mage::getSingleton('checkout/session')->setSessMpGroupId();
		}
		if ($groupCode = Mage::getSingleton('checkout/session')->getSessMpGroupCode()) {
			if ($groupId = $helper->checkIfGroupCodeIsValid($groupCode)) {
				$helper->log('$groupCode::' . $groupCode);
				$order->setCustomerGroupId($groupId);
				Mage::getSingleton('checkout/session')->setSessMpGroupCode();
			}
		}
	}

}