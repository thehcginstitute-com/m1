<?php
namespace HCG\MailChimp;
use Mage_Adminhtml_Block_System_Config_Form as F;
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
	static function isExtraEntry(C $c, string $s, int $sid, int $wid):bool {
		$cS = $c->getScope(); /** @var string $cS */
		$cSid = (int)$c->getScopeId(); /** @var int$cSid */
		return $cSid && (
			F::SCOPE_STORES === $cS && $s !== $cS
			|| F::SCOPE_WEBSITES === $cS && F::SCOPE_STORES === $s && $wid !== $cSid
			|| F::SCOPE_STORES === $cS && F::SCOPE_STORES === $s && $sid !== $cSid
		);
	}

	/**
	 * 2024-06-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by \Ebizmarts_MailChimp_Helper_Data::::getMailChimpScopeByStoreId()
	 * @used-by \Ebizmarts_MailChimp_Helper_Data::getApiByMailChimpStoreId()
	 * @used-by \HCG\MailChimp\Tags\ProcessMergeFields::_addSubscriberData()
	 * @return ?array(string => string|int)
	 */
	static function scopeByPathV(string $p, string $v):?array {return /** @var ?array(string => string|int) $d */
		!($d = df_fetch_one('core_config_data', '*', ['path' => $p, 'value' => $v]))
			? null
			: dfa($d, ['scope', 'scope_id'])
	;}
}