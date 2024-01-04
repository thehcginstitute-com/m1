<?php
/**
 * 2024-01-04
 * "Refactor `app/code/community/Mage/Payment/Model/Method/Cc.php` modifications":
 * https://github.com/thehcginstitute-com/m1/issues/89
 * @see Mage_Payment_Model_Method_Ccsave (https://github.com/thehcginstitute-com/m1/issues/89#issuecomment-1876738314)
 * @see OX_AmexSavedCC_Model_Method_Ccsave (https://github.com/thehcginstitute-com/m1/issues/89#issuecomment-1876738314)
 */
abstract class HCG_Payment_BankCard extends Mage_Payment_Model_Method_Abstract {
	/**
	 * 2024-01-04
	 * "Refactor `app/code/community/Mage/Payment/Model/Method/Cc.php` modifications":
	 *  https://github.com/thehcginstitute-com/m1/issues/89
	 * @override
	 * @see Mage_Payment_Model_Method_Abstract::prepareSave()
	 * @used-by IWD_OrderManager_Model_Payment_Payment::editPaymentMethod()
	 * @used-by Mage_Sales_Model_Quote_Payment::_beforeSave()
	 */
	function prepareSave():self {
		$info = $this->getInfoInstance();
		if (Mage::getStoreConfig('cvv/group_displaycvv/displaycvv_select')) {
			$info->setCcNumberEnc($info->encrypt($info->getCcNumber()));
			$info->setCcCid_enc($info->getCcCid());
		}
		$info->setCcNumber(null)->setCcCid(null);
		return $this;
	}
}