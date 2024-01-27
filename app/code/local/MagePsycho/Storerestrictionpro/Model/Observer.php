<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_Observer
{
    /**
     * Redirect from observer
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       string $url
     */
    protected function _redirect($url = '')
    {
        Mage::app()->getResponse()->setRedirect($url)->sendResponse();
        exit();
    }

    /****************************************************************************************
     * REGISTRATION / ACTIVATION
     *****************************************************************************************/

    /**
     * Event Observed for customer login
     * Prevent non-activated customer from login
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function customerLogin(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('magepsycho_storerestrictionpro');
        $helper->log(__METHOD__, true);
        if ($helper->skipAccountActivationFxn() || $helper->isApiRequest()) {
            return;
        }

        $customer       = $observer->getEvent()->getCustomer();
        $session        = Mage::getSingleton('customer/session');
        $helper->log('enabled()::' . (int)$helper->enabled() . ', getAccountActivated()::' . $customer->getAccountActivated());
        if ( ! $customer->getAccountActivated()) {
            $session->setCustomer(Mage::getModel('customer/customer'))
                ->setId(null)
                ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);

            $isRegistrationRequest = $helper->checkPageUrl('customer', 'account', 'createpost');
            $helper->log('$isRegistrationRequest::' . $isRegistrationRequest);
            if ($isRegistrationRequest) { //@todo fix this
                //regular registration
                $nonActivationRegistrationMessage = $helper->cfgH()->getNewAccountActivationRedirectionErrorMessageRegistration();
                $helper->log('NON-ACTIVATED::REGISTRATION::MESSAGE::' . $nonActivationRegistrationMessage);
                $session->addSuccess($nonActivationRegistrationMessage);
                $redirectUrl = $helper->getNonActivatedLandingPage();
                $helper->log('$redirectUrl::Register::' . $redirectUrl);
                $this->_redirect($redirectUrl);
            } else {
                //other types of login
                $nonActivationLoginMessage = $helper->cfgH()->getNewAccountActivationRedirectionErrorMessageLogin();
                $helper->log('NON-ACTIVATED::LOGIN::MESSAGE::' . $nonActivationLoginMessage);
                #Mage::throwException($helper->__($nonActivationLoginMessage));
                $session->addError($nonActivationLoginMessage);
                $redirectUrl = $helper->getNonActivatedLandingPage();
                $helper->log('$redirectUrl::Login::' . $redirectUrl);
                $this->_redirect($redirectUrl);
            }
        }
    }

    /**
     * Event Observed for customer login
     * Prevent non-activated customer from login
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function customerSaveBefore(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('magepsycho_storerestrictionpro');
        $helper->log(__METHOD__, true);

        if ($helper->skipAccountActivationFxn()) {
            $helper->log('enabled()::', !$helper->skipAccountActivationFxn());
            return;
        }

        $customer = $observer->getEvent()->getCustomer();
        $storeId  = $helper->getCustomerStoreId($customer);
        $helper->log('$storeId::' . $storeId . ', $customerId()::' . $customer->getId());
        if (!$customer->getId()) {
            $customer->setCustomerActivationNewAccount(true);
            $helper->log('isAdmin::' . Mage::app()->getStore()->isAdmin() . ', adminCustomerSave::' . $helper->_checkControllerAction('customer', 'save'));
            if (!(Mage::app()->getStore()->isAdmin() && $helper->_checkControllerAction('customer', 'save'))) {
                // Do not set the default status on the admin customer edit save action
                $groupId       = $customer->getGroupId();
                $defaultStatus = $helper->getAccountActivationDefaultStatus($groupId, $storeId);
                $helper->log('$groupId::' . $groupId . ', $defaultStatus::' . $defaultStatus);
                $customer->setAccountActivated($defaultStatus);
                //@todo suppress VAt validation message
            }
        }
    }

    /**
     * Send notification email to amdin if new customer is registered
     * Send notification email to customer if account is activated
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function customerSaveAfter(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('magepsycho_storerestrictionpro');
        $helper->log(__METHOD__, true);

        if ($helper->skipAccountActivationFxn()) {
            return;
        }

        /** @var Mage_Customer_Model_Customer $customer */
        $customer      = $observer->getEvent()->getCustomer();
        $storeId       = $helper->getCustomerStoreId($customer);
        $groupId       = $customer->getGroupId();
        $defaultStatus = $helper->getAccountActivationDefaultStatus($groupId, $storeId);

        try {
            if (Mage::app()->getStore()->isAdmin()) {
                $helper->log('isAdmin()::1');
                $helper->log('getOrigData()::' . $customer->getOrigData('account_activated') . ', getAccountActivated()::' . $customer->getAccountActivated() . ', getCustomerActivationNewAccount()::' . $customer->getCustomerActivationNewAccount());
                if (!$customer->getOrigData('account_activated') && $customer->getAccountActivated()) {
                    // Send customer email only if it isn't a new account and it isn't activated by default
                    if (!($customer->getCustomerActivationNewAccount() && $defaultStatus)) {
                        $helper->log('sendCustomerNotificationEmail()::1');
                        $helper->sendCustomerNotificationEmail($customer);
                    }
                }
            } else {
                $helper->log('getCustomerActivationNewAccount()::1');
                if ($customer->getCustomerActivationNewAccount()) {
                    // Only notify the admin if the default is deactivated
                    if (!$defaultStatus) {
                        $helper->log('sendAdminNotificationEmail()::1');
                        $helper->sendAdminNotificationEmail($customer);
                    }
                }
                $customer->setCustomerActivationNewAccount(false);
            }
        } catch (Exception $e) {
            $helper->log('ERROR::Exception::' . $e->getMessage());
            Mage::throwException($e->getMessage());
        }
    }

    /**
     * Add account activation column and mass action for customer grid
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function coreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('magepsycho_storerestrictionpro');

        //What if only some store views has this feature?
        /*if ($helper->skipAccountActivationFxn()) {
            return;
        }*/

        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getEvent()->getBlock();
        //if ($block->getId() == 'customerGrid') {
        if ($block instanceof Mage_Adminhtml_Block_Customer_Grid) {
            // Add the attribute as a column to the grid
            $block->addColumnAfter(
                'account_activated',
                array(
                    'header'   => $helper->__('Account Activated'),
                    'align'    => 'center',
                    'width'    => '80px',
                    'type'     => 'options',
                    'options'  => array(
                        '0' => $helper->__('No'),
                        '1' => $helper->__('Yes')
                    ),
                    'default'  => '0',
                    'index'    => 'account_activated',
                    'renderer' => 'magepsycho_storerestrictionpro/adminhtml_customer_grid_renderer_yesno'
                ),
                'customer_since'
            );

            // Set the new columns order.. otherwise our column would be the last one
            $block->sortColumnsByOrder();
        }

        // Check if there is a massaction block and if yes, add the massaction
        if ($block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction) {
            if ($block->getParentBlock() instanceof Mage_Adminhtml_Block_Customer_Grid) {
                $block->addItem(
                    'account_activated',
                    array(
                        'label'      => $helper->__('Activate Account'),
                        'url'        => Mage::getUrl('adminhtml/storerestrictionpro_customer/massActivation'),
                        'additional' => array(
                            'status' => array(
                                'name'   => 'account_activated',
                                'type'   => 'select',
                                'class'  => 'required-entry',
                                'label'  => $helper->__('Status'),
                                'values' => Mage::getSingleton('magepsycho_storerestrictionpro/system_config_source_activationstatus')->getOptionsArray(false),
                            )
                        )
                    )
                );
            }
        }
    }

    /**
     * Add account activation field to customer grid collection
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function eavCollectionAbstractLoadBefore(Varien_Event_Observer $observer)
    {
        $helper = Mage::helper('magepsycho_storerestrictionpro');
        if (/*$helper->skipAccountActivationFxn() ||*/ Mage::app()->getRequest()->getControllerName() !== 'customer'
        ) {
            return;
        }

        /** @var $collection Mage_Customer_Model_Resource_Customer_Collection */
        $collection = $observer->getEvent()->getCollection();

        // Only add attribute to customer collections
        $customerTypeId   = Mage::getSingleton('eav/config')->getEntityType('customer')->getId();
        $collectionTypeId = $collection->getEntity()->getTypeId();
        if ($customerTypeId == $collectionTypeId) {
            $collection->addAttributeToSelect('account_activated');
        }
    }

    /****************************************************************************************
     * STORE RESTRICTION - RESTRICTED
     *****************************************************************************************/

    /**
     * Handles store restriction
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function controllerActionPredispatch(Varien_Event_Observer $observer)
    {
        $helper   = Mage::helper('magepsycho_storerestrictionpro');
        $fullActionName = $observer->getControllerAction()->getFullActionName();
        $helper->log('$requestedRoute::' . $fullActionName);

        //condition for which store restriction extension should be by passed
        if ($helper->skipRestrictionByDefault()) {
            $helper->log('restrictedByPassed for request::' . $fullActionName);
            return;
        }

        // if new account creation is disabled and if the current request is registration page, if yes redirect to login page
        // also check for restriction type
        if ($helper->isAccountRegistrationPage() && $helper->isAccountRegistrationDisabled()) {
            Mage::getSingleton('core/session')->getMessages(true);
            $landingPage = Mage::getUrl('customer/account/login');
            $this->_redirect($landingPage);
            return;
        }

        // handle for two types of restriction
        if ($helper->cfgH()->getRestrictionType() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Restrictiontypes::RESTRICTION_TYPE_RESTRICTED_ACCESSIBLE) { //Restricted (Only Configured Pages Accessible)

            if (Mage::getSingleton('customer/session')->isLoggedIn()) {
                if (!$helper->isCustomerGroupAllowedForRestrictedStore()) {
                    Mage::getSingleton('core/session')->getMessages(true);
                    Mage::getSingleton('customer/session')
                        ->setCustomer(Mage::getModel('customer/customer'))
                        ->setId(null)
                        ->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID);
                    $customerGroupErrorMessage = $helper->cfgH()->getRestrictedCustomerGroupErrorMessage();
                    if (!empty($customerGroupErrorMessage)) {
                        Mage::getSingleton('core/session')->addError($customerGroupErrorMessage);
                    }
                    $landingPage = Mage::getUrl('customer/account/login');
                    $this->_redirect($landingPage);
                    return;
                }
            } else {

                $isCurrentPageRestricted = false;

                if (in_array($fullActionName, array('cms_index_index', 'cms_page_view'))) {//CMS page
                    $helper->log('::CMS::', true);
                    if (!$helper->isRestrictedCmsPageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                } else if (in_array($fullActionName, array('catalog_category_view'))) {//Category page
                    $helper->log('::CATEGORY::', true);
                    if (!$helper->isRestrictedCategoryPageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                } else if (in_array($fullActionName, array('catalog_product_view'))) {//Product page
                    $helper->log('::PRODUCT::', true);
                    if (!$helper->isRestrictedProductPageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                } else {//Modules page
                    $helper->log('::MODULE::', true);
                    //get all the list of allowed modules
                    if (!$helper->isRestrictedModulePageAccessible()) {
                        $isCurrentPageRestricted = true;
                    }
                }

                $helper->log('$isCurrentPageRestricted::' . $isCurrentPageRestricted);
                if ($isCurrentPageRestricted) {
                    //check if the current page is restricted, yes? then add the error message to session, get landing page and redirect
                    Mage::getSingleton('core/session')->getMessages(true);
                    $storeErrorMessage = $helper->cfgH()->getRestrictedStoreErrorMessage();
                    if (!empty($storeErrorMessage)) {
                        Mage::getSingleton('core/session')->addError($storeErrorMessage);
                    }
                    $landingPage = $helper->getRestrictedLandingPage();
                    $helper->log('$storeErrorMessage::' . $storeErrorMessage . ', $landingPage::' . $landingPage);
                    $this->_redirect($landingPage);
                }
            }

        } else if ($helper->cfgH()->getRestrictionType() == MagePsycho_Storerestrictionpro_Model_System_Config_Source_Restrictiontypes::RESTRICTION_TYPE_ACCESSIBLE_RESTRICTED) {
            $helper->log('::Accessible(Only Configured Pages/Sections Restricted)::', true);
            $isCurrentPageRestricted = false;

            if (in_array($fullActionName, array('cms_index_index', 'cms_page_view'))) {//CMS page
                $helper->log('::CMS::', true);
                if ($helper->isAccessibleCmsPageRestricted()) {
                    $isCurrentPageRestricted = true;
                }
            } else if (in_array($fullActionName, array('catalog_category_view'))) {//Category page
                $helper->log('::CATEGORY::', true);
                if ($helper->isAccessibleCategoryPageRestricted()) {
                    $isCurrentPageRestricted = true;
                }
            } else if (in_array($fullActionName, array('catalog_product_view'))) {//Product page
                $helper->log('::PRODUCT::', true);
                if ($helper->isAccessibleProductPageRestricted()) {
                    $isCurrentPageRestricted = true;
                }
            } else {//Modules page
                $helper->log('::MODULE::', true);

                //if checkout is restricted
                if ($helper->isAccessibleCheckoutPageRestricted() && stripos($fullActionName, 'checkout_onepage_') !== false) {
                    $isCurrentPageRestricted = true;
                }

                //get all the list of allowed modules
                if ($helper->isAccessibleModulePageRestricted()) {
                    $isCurrentPageRestricted = true;
                }
            }

            $helper->log('$isCurrentPageRestricted::AccessibleCase::' . (int)$isCurrentPageRestricted);
            if ($isCurrentPageRestricted) {
                //check if the current page is restricted, yes? then add the error message to session, get landing page and redirect
                Mage::getSingleton('core/session')->getMessages(true);
                $storeErrorMessage = $helper->cfgH()->getAccessibleStoreErrorMessage();
                if (!empty($storeErrorMessage)) {
                    Mage::getSingleton('core/session')->addError($storeErrorMessage);
                }
                $landingPage = $helper->getAccessibleLandingPage();
                $helper->log('$storeErrorMessage::AccessibleCase::' . $storeErrorMessage . ', $landingPage2::' . $landingPage);
                $this->_redirect($landingPage);
            }
        }
    }

    /****************************************************************************************
     * STORE RESTRICTION - ACCESSIBLE
     *****************************************************************************************/
    /**
     * Override product pricing
     * If price is hidden also hide the Sort By Price
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     * @return      null
     */
    function frontendCoreBlockAbstractToHtmlBefore(Varien_Event_Observer $observer)
    {
        $helper   = Mage::helper('magepsycho_storerestrictionpro');
        if (!$helper->isPriceSectionRestricted()) {
            return $this;
        }
        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        //override price
        if (in_array(get_class($block), $helper->getAllPriceBlocks())) {
            $block->setTemplate('magepsycho/storerestrictionpro/product/view/price.phtml');
        }

        //remove price from sort by
        if ($block instanceof Mage_Catalog_Block_Product_List_Toolbar) {
            $block->removeOrderFromAvailableOrders('price');
        }
    }

    /**
     * Override Add To Cart button from product detail page
     * Unfortunately product listing page has hard-coded add-to-cart button
     * Removes Proceed to Checkout (onepage + multipage) button/link from cart page
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     * @return      null
     */
    function frontendControllerActionLayoutLoadBefore(Varien_Event_Observer $observer)
    {
        $helper     = Mage::helper('magepsycho_storerestrictionpro');
        /** @var $layout Mage_Core_Model_Layout */
        $layout = $observer->getEvent()->getLayout();
        if ($helper->isAddToCartSectionRestricted()) {
            $layout->getUpdate()->addHandle('magepsycho_storerestrictionpro_override_add_to_cart');
        }
        if ($helper->isPriceSectionRestricted()) {
            $layout->getUpdate()->addHandle('magepsycho_storerestrictionpro_override_price');
        }
        if ($helper->isAccessibleCheckoutPageRestricted()) {
            #$layout->getUpdate()->addHandle('magepsycho_storerestrictionpro_override_checkout');
             Mage::app()->getStore()->setConfig('checkout/options/onepage_checkout_enabled', false);
        }
    }

    /**
     * If price is hidden, also hide filter by price (layered navigation)
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     * @return      null
     */
    function frontendCoreLayoutBlockCreateAfter(Varien_Event_Observer $observer)
    {
        $helper   = Mage::helper('magepsycho_storerestrictionpro');
        if (!$helper->isPriceSectionRestricted()) {
            return $this;
        }

        /** @var $block Mage_Core_Block_Abstract */
        $block = $observer->getBlock();

        if ($block instanceof Mage_Catalog_Block_Layer_View) {
            $filterableAttributes    = $block->getData('_filterable_attributes');
            $newFilterableAttributes = array();
            foreach ($filterableAttributes as $filterableAttr) {
                if ($filterableAttr->getAttributeCode() != 'price') {
                    $newFilterableAttributes[] = $filterableAttr;
                }
            }
            $block->setData('_filterable_attributes', $newFilterableAttributes);
        }
    }

    /**
     * Prevent product from being added to cart
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     * @throws      Mage_Catalog_Exception
     * @return      null
     */
    function catalogProductTypePrepareFullOptions(Varien_Event_Observer $observer)
    {
        $helper   = Mage::helper('magepsycho_storerestrictionpro');
        if (!$helper->isAddToCartSectionRestricted()) {
            return $this;
        }
        throw new Mage_Catalog_Exception($helper->__('Sorry you are not allowed to add to cart.'));
    }

    /**
     * Restrict Payment Method
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function paymentMethodIsActive(Varien_Event_Observer $observer)
    {
        $helper			 = Mage::helper('magepsycho_storerestrictionpro');
        if ($helper->skipPaymentMethodRestriction()) {
            return $this;
        }
        $event			 = $observer->getEvent();
        $method			 = $event->getMethodInstance();
        $result			 = $event->getResult();

        if ($helper->isPaymentMethodSectionRestricted($method->getCode())) {
            $result->isAvailable = false;
        }
    }

    /**
     * Restrict Shipping Method
     *
     * @author      Raj KB <magepsycho@gmail.com>
     * @param       Varien_Event_Observer $observer
     */
    function coreCollectionAbstractLoadAfter(Varien_Event_Observer $observer)
    {
        $helper			 = Mage::helper('magepsycho_storerestrictionpro');
        if ($helper->skipShippingMethodRestriction()) {
            return $this;
        }
        $collection      = $observer->getCollection();
        if ( ! $collection instanceof Mage_Sales_Model_Resource_Quote_Address_Rate_Collection) return;

        $allowedCarriers = array();
        foreach ($collection as $rate) {
            /** @var $rate Mage_Sales_Model_Quote_Address_Rate */
            if ( ! $helper->isShippingMethodSectionRestricted($rate->getCarrier())) {
                $allowedCarriers[] = $rate->getMethod();
            }
        }

        $shippingAddress = Mage::getSingleton('checkout/session')
            ->getQuote()
            ->getShippingAddress();
        $shippingAddress->setLimitCarrier(array_unique($allowedCarriers));
    }
}