<?php
namespace HCG\MailChimp;
use Mage_Core_Model_Config_Data as C;
# 2024-06-08
# Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
# to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
final class Cfg {
	/**
	 * 2024-06-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by hcg_mc_cfg_scope()
	 */
	static function isExtraEntry(C $c, string $scope, int $scopeId, $websiteId):bool {
		$h = hcg_mc_h();
		return
			$h->isNotDefaultScope($c) && ($h->isIncorrectScope($c, $scope)
			|| $h->isDifferentWebsite($c, $scope, $websiteId)
			|| $h->isDifferentStoreView($c, $scope, $scopeId))
		;
	}
}