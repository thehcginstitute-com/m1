<?php
namespace HCG\MagePsycho;
# 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
abstract class Helper extends \Mage_Core_Helper_Abstract {
	/**
	 * 2024-01-27
	 * @used-by self::cfg()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::log()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::log()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::log()
	 * @see \MagePsycho_Customerregfields_Helper_Data::moduleMf()
	 * @see \MagePsycho_Loginredirectpro_Helper_Data::moduleMf()
	 * @see \MagePsycho_Storerestrictionpro_Helper_Data::moduleMf()
	 */
	abstract protected function moduleMf():string;

	/** 2024-01-27 */
	final function __construct() {
		list($k, $this->_mode) = $this->cfg('option/domain_type')
			? ['prod_license', 'production']
			: ['dev_license', 'development']
		;
		$this->_temp = $this->cfg('option/' . $k);
	}

	/**
	 * 2024-01-27
	 * @used-by self::__construct()
	 */
    final protected function cfg(string $xmlPath, $storeId = null) {return \Mage::getStoreConfig(
		$this->moduleMf() . '/' . $xmlPath, $storeId
	);}

	/**
	 * 2024-01-27
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isValid()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::isValid()
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::isValid()
	 */
	final protected function checkEntry(string $domain, string $serial):bool {
		$salt = sha1($this->moduleL());
		if(sha1($salt . $domain . $this->_mode) == $serial) {
			return true;
		}

		return false;
	}

	/**
	 * 2024-01-27
	 * @used-by self::checkEntry()
	 * @see \MagePsycho_Customerregfields_Helper_Data::moduleL()
	 */
	protected function moduleL():string {return df_last(explode('_', $this->moduleMf()));}

	/**
	 * 2024-01-27
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::isValid()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::isValid()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::isValid()
	 */
	final protected function temp():string {return $this->_temp;}

	/**
	 * 2024-01-27
	 * @used-by self::__construct()
	 * @used-by self::checkEntry()
	 * @var string
	 */
	private $_mode;

	/**
	 * 2024-01-27
	 * @used-by self::__construct()
	 * @used-by self::temp()
	 * @var string
	 */
	private $_temp;
}