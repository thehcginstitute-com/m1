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
class Ebizmarts_MailChimp_Model_System_Config_Backend_Store extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        $helper = $this->makeHelper();
        $scopeId = $this->getScopeId(); /** @var int $scopeId */
        $scope = $this->getScope();
        $groups = $this->getData('groups');
        $newMailchimpStoreId = (isset($groups['general']['fields']['storeid']['value']))
            ? $groups['general']['fields']['storeid']['value']
            : null;
        $mcStoreOld = hcg_mc_sid($scopeId); /** @var ?string $mcStoreOld */
        $isSyncing = $helper->getMCIsSyncing($newMailchimpStoreId, $scopeId, $scope);
        $helper->cancelAllPendingBatches($mcStoreOld);
        $helper->restoreAllCanceledBatches($newMailchimpStoreId);
        if ($this->isValueChanged() && $this->getValue()) {
            $helper->deletePreviousConfiguredMCStoreLocalData($mcStoreOld, $scopeId, $scope);
            if ($isSyncing === null) {
                hcg_mc_cfg_save(
					Ebizmarts_MailChimp_Model_Config::GENERAL_MCISSYNCING . "_$newMailchimpStoreId"
					,true
					,$scopeId
					,$scope
				);
            }
        }
    }

    /**
     * @return Ebizmarts_MailChimp_Helper_Data
     */
    protected function makeHelper() {return hcg_mc_h();}

    /**
     * @return Ebizmarts_MailChimp_Helper_Date
     */
    protected function makeDateHelper()
    {
        return Mage::helper('mailchimp/date');
    }

    /**
     * @return Mage_Adminhtml_Model_Session
     */
    protected function getAdminSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }
}
