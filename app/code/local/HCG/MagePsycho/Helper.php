<?php
namespace HCG\MagePsycho;
# 2024-01-27 "Refactor the `MagePsycho_*` modules": https://github.com/thehcginstitute-com/m1/issues/331
abstract class Helper extends \Mage_Core_Helper_Abstract {
	/** 2024-01-27 */
	final function __construct() {
		$field = base64_decode('ZG9tYWluX3R5cGU=');
		if ($this->getConfigValue('option/' . $field) == 1) {
			$key        = base64_decode('cHJvZF9saWNlbnNl');
			$this->_mode = base64_decode('cHJvZHVjdGlvbg==');
		} else {
			$key        = base64_decode('ZGV2X2xpY2Vuc2U=');
			$this->_mode = base64_decode('ZGV2ZWxvcG1lbnQ=');
		}
		$this->_temp = $this->getConfigValue('option/' . $key);
	}

	/**
	 * 024-01-27
	 * @used-by \MagePsycho_Customerregfields_Helper_Data::checkEntry()
	 * @used-by \MagePsycho_Loginredirectpro_Helper_Data::checkEntry()
	 * @used-by \MagePsycho_Storerestrictionpro_Helper_Data::checkEntry()
	 */
	final protected function mode():string {return $this->_mode;}

	/**
	 * 024-01-27
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