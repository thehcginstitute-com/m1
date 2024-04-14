<?php
use Ebizmarts_MailChimp_Helper_Data as H;
/**
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
			$r = ['scope_id' => $config->getScopeId(), 'scope' => $config->getScope()];
			break;
		case 'websites':
			if (!$r || $r['scope'] == 'default') {
				$r = ['scope_id' => $config->getScopeId(), 'scope' => $config->getScope()];
			}
			break;
		case 'default':
			# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# «Trying to access array offset on value of type null
			# in app/code/community/Ebizmarts/MailChimp/Helper/Data.php on line 2134»:
			# https://github.com/thehcginstitute-com/m1/issues/495
			if ($r && 'stores' !== dfa($r, 'scope')) {
				$r = ['scope_id' => $config->getScopeId(), 'scope' => $config->getScope()];
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