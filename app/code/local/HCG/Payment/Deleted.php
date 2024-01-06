<?php
# 2023-12-16
# 1) "Delete unused payment modules": https://github.com/thehcginstitute-com/m1/issues/47
# 2) "«The requested Payment Method is not available»
# on viewing an order paid via a deleted payment module":
# https://github.com/thehcginstitute-com/m1/issues/52
final class HCG_Payment_Deleted extends Mage_Payment_Model_Method_Abstract {
	/**
	 * 2023-12-16
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::getTitle()
	 */
	function getTitle():string {return "A deleted payment method: «{$this->_m}»";}

	/**
	 * 2023-12-16
	 * @used-by HCG_Payment_Model_Info::getMethodInstance()
	 */
	function setOriginalMethod(string $m):void {$this->_m = $m;}

	/**
	 * 2023-12-16
	 * @used-by self::$_code
	 * @used-by HCG_Payment_Model_Info::getMethodInstance()
	 */
	const CODE = 'hcg_deleted';

	/**
	 * 2023-12-16
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::$_code
	 */
	protected $_code  = self::CODE;

	/**
	 * 2023-12-16
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::$_formBlockType
	 */
	protected $_formBlockType = 'payment/form_cashondelivery';

	/**
	 * 2023-12-16
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::$_infoBlockType
	 */
	protected $_infoBlockType = 'payment/info';

	/**
	 * 2023-12-16
	 * @used-by self::getTitle()
	 * @used-by self::setOriginalMethod()
	 * @var string
	 */
	private $_m;
}