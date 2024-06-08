<?php
use HCG\MailChimp\Cfg as hCfg;
use Mage_Adminhtml_Block_System_Config_Form as F;
use Mage_Core_Model_Config_Data as C;
use Mage_Core_Model_Resource_Config_Data_Collection as CC;
/**
 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * 2024-06-08
 * Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
 * to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::handleWebhookChange()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 */
function hcg_mc_cfg_scope(string $path, int $scopeId, string $scope = 'stores'):array {
	$websiteId = null;
	$cc = df_config_c()
		->addFieldToFilter('path', ['eq' => $path])
		->addFieldToFilter('scope_id', ['in' =>
			F::SCOPE_STORES !== $scope
				? [$scopeId, 0]
				: [$scopeId, $websiteId = df_store($scopeId)->getWebsiteId(), 0]
		]); /** @var CC $cc */
	$r = []; /** @var array $r */
	foreach ($cc as $c) { /** @var C $c */
		if (hCfg::isExtraEntry($c, $scope, $scopeId, $websiteId)) {
			continue;
		}
		switch ($c->getScope()) {
			case F::SCOPE_STORES:
				$r = ['scope_id' => $c->getScopeId(), 'scope' => $c->getScope()];
				break;
			case 'websites':
				if (!$r || $r['scope'] == 'default') {
					$r = ['scope_id' => $c->getScopeId(), 'scope' => $c->getScope()];
				}
				break;
			case 'default':
				# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# «Trying to access array offset on value of type null
				# in app/code/community/Ebizmarts/MailChimp/Helper/Data.php on line 2134»:
				# https://github.com/thehcginstitute-com/m1/issues/495
				if ($r && 'stores' !== dfa($r, 'scope')) {
					$r = ['scope_id' => $c->getScopeId(), 'scope' => $c->getScope()];
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