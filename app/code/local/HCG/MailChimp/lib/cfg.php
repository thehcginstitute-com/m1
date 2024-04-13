<?php
use Ebizmarts_MailChimp_Helper_Data as H;
use Mage_Core_Model_Config as Cfg;
/*
 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::handleWebhookChange()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 */
function hcg_mc_cfg_scope(string $path, int $scopeId, $scope = 'stores'):array {
	$websiteId = null;
	$configCollection = Mage::getResourceModel('core/config_data_collection')
		->addFieldToFilter('path', ['eq' => $path])
		->addFieldToFilter('scope_id', ['in' =>
			'stores' !== $scope
				? [$scopeId, 0]
				: [$scopeId, $websiteId = df_store($scopeId)->getWebsiteId(), 0]
		]);
	$r = []; /** @var array $r */
	$h = Mage::helper('mailchimp'); /** @var H $h */
	foreach ($configCollection as $config) {
		//Discard possible extra website or store
		if ($h->isExtraEntry($config, $scope, $scopeId, $websiteId)) {
			continue;
		}
		switch ($config->getScope()) {
		case 'stores':
			$r = array('scope_id' => $config->getScopeId(), 'scope' => $config->getScope());
			break;
		case 'websites':
			if (!$r || $r['scope'] == 'default') {
				$r = array('scope_id' => $config->getScopeId(), 'scope' => $config->getScope());
			}
			break;
		case 'default':
			# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# «Trying to access array offset on value of type null
			# in app/code/community/Ebizmarts/MailChimp/Helper/Data.php on line 2134»:
			# https://github.com/thehcginstitute-com/m1/issues/495
			if ($r && 'stores' !== dfa($r, 'scope')) {
				$r = array('scope_id' => $config->getScopeId(), 'scope' => $config->getScope());
			}
			break;
		}
	}
	# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# «Trying to access array offset on value of type null
	# in app/code/community/Ebizmarts/MailChimp/Model/Api/Subscribers.php on line 92»:
	# https://github.com/thehcginstitute-com/m1/issues/504
	# 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# «`Ebizmarts_MailChimp`: «Column 'scope' cannot be null, query was:
	# INSERT INTO `core_config_data` (`scope`, `scope_id`, `path`, `value`) VALUES (?, ?, ?, ?)»:
	# https://github.com/thehcginstitute-com/m1/issues/508
	return $r ?: [0, 'default'];
}

/**
 * 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Adminhtml_MergevarsController::saveaddAction()
 * @used-by Ebizmarts_MailChimp_Helper_Data::deleteAllConfiguredMCStoreLocalData()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMCIsSyncing()
 * @used-by Ebizmarts_MailChimp_Helper_Data::retrieveAndSaveMCJsUrlInConfig()
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
function hcg_mc_cfg_save(string $path, string $v, int $scopeId = 0, string $scope = 'default', bool $cleanCache = true):void {
	$c = Mage::getConfig(); /** @var Cfg $c */
	$c->saveConfig($path, $v, $scope, $scopeId);
	if ($cleanCache) {
		df_cache_clean_cfg();
	}
}

/**
 * 2024-04-13 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Helper_Data::saveLastItemsSent()
 */
function hcg_mc_cfg_save_a(array $vv, int $scopeId = 0, string $scope = 'default', bool $cleanCache = true):void {
	$c = Mage::getConfig(); /** @var Cfg $c */
	foreach ($vv as $v) {/** @var string[] $v */
		$c->saveConfig($v[0], $v[1], $scope, $scopeId);
	}
	if ($cleanCache) {
		df_cache_clean_cfg();
	}
}