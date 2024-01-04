<?php
/**
 * 2024-01-04
 * 1) "Refactor `app/code/community/Mage/Payment/Model/Method/Cc.php` modifications":
 * https://github.com/thehcginstitute-com/m1/issues/89
 * 2) This class overrides @see Mage_Payment_Model_Method_Ccsave
 */
final class HCG_Payment_VisaOrMasterCard extends HCG_Payment_BankCard {
	/**
	 * 2024-01-04
	 * @see Mage_Payment_Model_Method_Ccsave::$_code
	 * @var string
	 */
	protected $_code = 'ccsave';

	/**
	 * 2024-01-04
	 * @see Mage_Payment_Model_Method_Ccsave::$_formBlockType
	 * @var string
	 */
	protected $_formBlockType = 'payment/form_ccsave';

	/**
	 * 2024-01-04
	 * @see Mage_Payment_Model_Method_Ccsave::$_infoBlockType
	 * @var string
	 */
	protected $_infoBlockType = 'payment/info_ccsave';
}