<?php
namespace HCG\MailChimp;
use Ebizmarts_MailChimp_Model_Config as eC;
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
	 * Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
	 * to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
	 * @used-by self::scopeByStoreId()
	 * @used-by \Ebizmarts_MailChimp_Helper_Data::getApiByMailChimpStoreId()
	 * @used-by \HCG\MailChimp\Tags\ProcessMergeFields::_addSubscriberData()
	 * @return ?array(string => string|int)
	 */
	static function scopeByPathV(string $p, string $v):?array {return
		/**
		 * 2024-06-08
		 *	{
		 *		"config_id": "1600",
		 *		"path": "ewcore/_system/last_messages_updated_server_time",
		 *		"scope": "stores",
		 *		"scope_id": "1",
		 *		"updated_at": null,
		 *		"value": "1673841330"
		 *	}
		 * @var ?array(string => string|null) $d
		 */
		!($d = df_fetch_one('core_config_data', '*', ['path' => $p, 'value' => $v]))
			? null : ['scope_id' => (int)dfa($d, 'scope_id'), 'scope' => dfa($d, 'scope')]
	;}

	/**
	 * 2024-06-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * Transfer the configuration code from `Ebizmarts_MailChimp_Helper_Data`
	 * to a dedicated class (`HCG\MailChimp\Cfg`) and `hcg_mc_cfg_*` functions: https://github.com/thehcginstitute-com/m1/issues/641
	 * @used-by self::STUB()
	 * @return ?array(string => string|int)
	 */
	static function scopeByStoreId(int $sid):?array {return self::scopeByPathV(eC::GENERAL_MCSTOREID, hcg_mc_sid($sid));}
}