<?php
/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     8/4/16 5:56 PM
 * @file:     Apikey.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Backend_Apikey extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        $helper = $this->makeHelper();
        $scopeId = $this->getScopeId();
        $scope = $this->getScope();
        $valueChanged = $this->isValueChanged();

        if ($valueChanged && !$this->getValue()) {
            $configValue = array(array(Ebizmarts_MailChimp_Model_Config::GENERAL_ACTIVE, false));
            $helper->saveMailchimpConfig($configValue, $scopeId, $scope);
            $message = $helper->__(
                'Please note the extension has been disabled due to the lack of an api key or audience configured.'
            );
            $this->getAdminSession()->addWarning($message);
        }
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function makeHelper()
    {
        return Mage::helper('mailchimp');
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
