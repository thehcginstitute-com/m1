<?php
namespace HCG\MagePsycho;
# 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
abstract class Helper extends \Mage_Core_Helper_Abstract {
	/**
	 * 2024-01-27
	 * @used-by self::cfg()
	 * @used-by self::domain()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::log()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::log()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::log()
	 * @see \MagePsycho_Customerregfields_Helper_Data::moduleMf()
	 * @see \MagePsycho_Loginredirectpro_Helper_Data::moduleMf()
	 * @see \MagePsycho_Storerestrictionpro_Helper_Data::moduleMf()
	 */
	abstract protected function moduleMf():string;

	/**
	 * 2024-01-27
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::isFxnSkipped()
	 * @used-by \MagePsycho_Customerregfields_Model_Observer::adminhtmlControllerActionPredispatch()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::isFxnSkipped()
	 * @used-by \MagePsycho_Loginredirectpro_Model_Observer::adminhtmlPreDispatchCheck()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isFxnSkipped()
	 * @used-by \MagePsycho_Storerestrictionpro_Model_Observer::adminhtmlControllerActionPredispatch()
	 */
	final function isValid():bool {return true;}

	/**
	 * 2024-01-27
	 * @used-by self::__construct()
	 */
    final protected function cfg(string $xmlPath, $storeId = null) {return \Mage::getStoreConfig(
		$this->moduleMf() . '/' . $xmlPath, $storeId
	);}

	/**
	 * 2024-01-27
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::getMessage()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::getMessage()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::getMessage()
	 */
	final protected function domain():string {return strtolower(
		\Mage::helper("{$this->moduleMf()}/url")->getBaseDomain(\Mage::getBaseUrl())
	);}
}