<?php
/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     8/4/16 8:28 PM
 * @file:     List.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Backend_List extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        $groups = $this->getData('groups');
        $helper = $this->getMailchimpHelper();
        $webhookHelper = $this->getMailchimpWebhookHelper();
        $scopeId = $this->getScopeId();
        $scope = $this->getScope();
        $valueChanged = $this->isValueChanged();

        $moduleIsActive = (isset($groups['general']['fields']['active']['value']))
            ? $groups['general']['fields']['active']['value']
            : $helper->isMailChimpEnabled($scopeId, $scope);
        $apiKey = (isset($groups['general']['fields']['apikey']['value']))
            ? $groups['general']['fields']['apikey']['value']
            : $helper->getApiKey($scopeId, $scope);
        $thisScopeHasSubMinSyncDateFlag = $helper->getIfConfigExistsForScope(
            Ebizmarts_MailChimp_Model_Config::GENERAL_SUBMINSYNCDATEFLAG,
            $scopeId,
            $scope
        );

        if ($valueChanged && !$this->getValue()) {
            hcg_mc_cfg_save(Ebizmarts_MailChimp_Model_Config::GENERAL_ACTIVE, false, $scopeId, $scope);
            $message = $helper->__(
                'Please note the extension has been disabled due to the lack of an api key or audience configured.'
            );
            $this->getAdminSession()->addWarning($message);
        }

        if ($valueChanged && ($moduleIsActive || $thisScopeHasSubMinSyncDateFlag) && $this->getValue()) {
            hcg_mc_cfg_save(
				Ebizmarts_MailChimp_Model_Config::GENERAL_SUBMINSYNCDATEFLAG
				,hcg_mc_h_date()->formatDate(null, "Y-m-d H:i:s")
				,$scopeId
				,$scope
			);
        }

        if ($apiKey && $moduleIsActive && $valueChanged) {
            $webhookHelper->handleWebhookChange($scopeId, $scope);
        }
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function getMailchimpHelper()
    {
        return hcg_mc_h();
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Webhook
     */
    protected function getMailchimpWebhookHelper()
    {
        return Mage::helper('mailchimp/webhook');
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
