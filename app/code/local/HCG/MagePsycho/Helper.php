<?php
namespace HCG\MagePsycho;
# 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
abstract class Helper extends \Mage_Core_Helper_Abstract {
	/**
	 * 2024-01-27
	 * @used-by self::cfg()
	 * @see \MagePsycho_Customerregfields_Helper_Data::moduleMf()
	 * @see \MagePsycho_Loginredirectpro_Helper_Data::moduleMf()
	 * @see \MagePsycho_Storerestrictionpro_Helper_Data::moduleMf()
	 */
	abstract protected function moduleMf():string;

	/** 2024-01-27 */
	final function __construct() {
		if ($this->cfg('option/domain_type') == 1) {
			$key        = base64_decode('cHJvZF9saWNlbnNl');
			$this->_mode = base64_decode('cHJvZHVjdGlvbg==');
		} else {
			$key        = base64_decode('ZGV2X2xpY2Vuc2U=');
			$this->_mode = base64_decode('ZGV2ZWxvcG1lbnQ=');
		}
		$this->_temp = $this->cfg('option/' . $key);
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
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::checkEntry()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::checkEntry()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::checkEntry()
	 */
	final protected function mode():string {return $this->_mode;}

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
	 * @used-by self::mode()
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