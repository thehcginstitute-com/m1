<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Adminhtml_Storerestrictionpro_CustomerController extends Mage_Adminhtml_Controller_Action
{
    function massActivationAction()
    {
        $customerIds = $this->getRequest()->getParam('customer');
        if (!is_array($customerIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('magepsycho_storerestrictionpro')->__('Please select customer(s).')
            );
        } else {
            try {
                $paramValue       = $this->getRequest()->getParam('account_activated');
                $activationStatus = $this->_getActivationStatus($paramValue);

                $updatedCustomerIds = Mage::getResourceModel('magepsycho_storerestrictionpro/customer')
                    ->massSetActivationStatus(
                        $customerIds, $activationStatus
                    );

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('magepsycho_storerestrictionpro')->__(
                        'Total of %d record(s) were successfully saved', count($updatedCustomerIds)
                    )
                );

                if ($activationStatus) {
                    if ($this->_getActivationNotificationStatus($paramValue)) {
                        $this->_sendCustomerActivationNotificationEmails($updatedCustomerIds);
                    }
                } else {
                    if ($this->_getDeActivationNotificationStatus($paramValue)) {
                        $this->_sendCustomerDeActivationNotificationEmails($customerIds);
                    }
                    // finally delete it @todo make it configurable
                    $this->_deleteDeactivatedCustomers($customerIds);
                }

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }

        $this->_redirect('adminhtml/customer');
    }

    protected function _getActivationStatus($status)
    {
        switch ($status) {
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus::ACTIVATION_STATUS_ENABLED_WITH_MAIL:
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus::ACTIVATION_STATUS_ENABLED_WITHOUT_MAIL:
                $activationStatus = 1;
                break;
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus::ACTIVATION_STATUS_DISABLED_WITH_MAIL:
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus::ACTIVATION_STATUS_DISABLED_WITHOUT_MAIL:
            default:
                $activationStatus = 0;
                break;
        }
        return $activationStatus;
    }

    protected function _getActivationNotificationStatus($status)
    {
        switch ($status) {
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus::ACTIVATION_STATUS_ENABLED_WITH_MAIL:
                $notify = true;
                break;
            default:
                $notify = false;
                break;
        }
        return $notify;
    }

    protected function _getDeActivationNotificationStatus($status)
    {
        switch ($status) {
            case MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus::ACTIVATION_STATUS_DISABLED_WITH_MAIL:
                $notify = true;
                break;
            default:
                $notify = false;
                break;
        }
        return $notify;
    }

    protected function _sendCustomerActivationNotificationEmails(array $customerIds)
    {
        $helper    = Mage::helper('magepsycho_storerestrictionpro');
        $customers = Mage::getResourceModel('customer/customer_collection')
            ->addAttributeToFilter('entity_id', array('in' => $customerIds))
            ->addAttributeToSelect('*')
            ->addNameToSelect();
        foreach ($customers as $customer) {
            $helper->sendCustomerNotificationEmail($customer);
        }
    }

    protected function _sendCustomerDeActivationNotificationEmails(array $customerIds)
    {
        $helper    = Mage::helper('magepsycho_storerestrictionpro');
        $customers = Mage::getResourceModel('customer/customer_collection')
                         ->addAttributeToFilter('entity_id', array('in' => $customerIds))
                         ->addAttributeToSelect('*')
                         ->addNameToSelect();
        foreach ($customers as $customer) {
            $helper->sendCustomerDeActivationNotificationEmail($customer);
        }
    }

    protected function _deleteDeactivatedCustomers(array $customerIds)
    {
        foreach ($customerIds as $customerId) {
            $customer = Mage::getModel('customer/customer')
                ->load($customerId);
            if ($customer && $customer->getId()) {
                $customer->delete();
            }
        }

    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }
}