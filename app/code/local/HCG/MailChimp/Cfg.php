<?php
namespace HCG\MailChimp;
use Mage_Core_Model_Config_Data as C;
# 2024-06-08
# Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
# to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
final class Cfg {
	/**
	 * 2024-06-08
	 * Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
	 * to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
	 * @used-by hcg_mc_cfg_scope()
	 */
	static function isExtraEntry(C $c, string $s, int $sid, int $websiteId):bool {
		$h = hcg_mc_h();
		return $c->getScopeId() && (
			($c->getScope() == 'stores' && $s != 'stores')
			|| $h->isDifferentWebsite($c, $s, $websiteId)
			|| $h->isDifferentStoreView($c, $s, $sid)
		);
	}
}