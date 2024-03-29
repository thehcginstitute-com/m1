<?php
# 2024-01-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `app/code/community/Mage/Payment/Model/Method/Cc.php` modifications":
# https://github.com/thehcginstitute-com/m1/issues/89
class OX_AmexSavedCC_Model_Method_Ccsave extends HCG_Payment_BankCard {
	/**
	 * 2024-01-04
	 * @see Mage_Payment_Model_Method_Ccsave::$_code
	 * @var string
	 */
	protected $_code = 'amexsavedcc';

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