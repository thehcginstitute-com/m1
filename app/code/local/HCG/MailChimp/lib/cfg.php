<?php
use Mage_Core_Model_Config as Cfg;
/**
 * 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Adminhtml_MergevarsController::saveaddAction()
 * @used-by Ebizmarts_MailChimp_Helper_Data::deleteAllConfiguredMCStoreLocalData()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMCIsSyncing()
 * @used-by Ebizmarts_MailChimp_Helper_Data::retrieveAndSaveMCJsUrlInConfig()
 * @used-by Ebizmarts_MailChimp_Helper_Data::saveLastItemsSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::setResendTurn()
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::createNewWebhook()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_updateSyncingFlag()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::handleSyncingValue()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::replaceAllOrdersBatch()
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::createMailChimpStore()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Observer::cleanProductImagesCacheAfter()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Active::_afterSave()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Apikey::_afterSave()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Ecommerce::_afterSave()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_List::_afterSave()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Store::_afterSave()
 * @used-by app/code/community/Ebizmarts/MailChimp/sql/mailchimp_setup/mysql4-upgrade-1.1.20-1.1.21.php
 * @used-by app/code/community/Ebizmarts/MailChimp/sql/mailchimp_setup/mysql4-upgrade-1.1.5-1.1.5.6.php
 * @used-by app/code/community/Ebizmarts/MailChimp/sql/mailchimp_setup/mysql4-upgrade-1.1.6.3-1.1.6.4.php
 * @used-by app/code/community/Ebizmarts/MailChimp/sql/mailchimp_setup/mysql4-upgrade-1.1.6.4-1.1.6.5.php
 */
function hcg_mc_cfg_save_a(array $vv, int $scopeId = 0, string $scope = 'default', bool $cleanCache = true):void {
	$c = Mage::getConfig(); /** @var Cfg $c */
	foreach ($vv as $v) {/** @var string[] $v */
		$c->saveConfig($v[0], $v[1], $scope, $scopeId);
	}
	if ($cleanCache) {
		$c->cleanCache();
	}
}