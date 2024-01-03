<?php
# 2024-01-03
# 1) "Transfer `app/code/local/Mage/Payment/Model/Method/Free.php` to the `HCG_Payment` module":
# https://github.com/thehcginstitute-com/m1/issues/81
# 2) This method is used in the Magento backend only.
final class HCG_Payment_Backend extends Mage_Payment_Model_Method_Free {
	/**
	 * 2024-01-03 This method is used in the Magento backend only.
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::canUseCheckout()
	 */
	function canUseCheckout():bool {return false;}

	/**
	 * 2024-01-03 This method is used in the Magento backend only.
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::canUseInternal()
	 */
	function canUseInternal():bool {return true;}

	/**
	 * 2024-01-03 «How to call grandparent method» https://stackoverflow.com/a/31150695
	 * @override
	 * @see Mage_Payment_Model_Method_Free::isAvailable()
	 */
	function isAvailable($q = null):bool {return Mage_Payment_Model_Method_Abstract::isAvailable($q) && !empty($q);}
}