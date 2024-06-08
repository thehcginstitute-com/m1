<?php
namespace HCG\MailChimp;
# 2024-06-08
# Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
# to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
final class Cfg {
	/**
	 * 2024-06-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by hcg_mc_cfg_scope()
	 */
	static function isExtraEntry($config, $scope, $scopeId, $websiteId):bool {
		$h = hcg_mc_h();
		return $h->isNotDefaultScope($config)
			&& ($h->isIncorrectScope($config, $scope)
				|| $h->isDifferentWebsite($config, $scope, $websiteId)
				|| $h->isDifferentStoreView($config, $scope, $scopeId));
	}
}