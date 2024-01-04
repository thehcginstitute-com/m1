<?php
/**
 * 2024-01-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor `app/code/community/Mage/Payment/Model/Method/Cc.php` modifications":
 * https://github.com/thehcginstitute-com/m1/issues/89
 * @see Mage_Payment_Model_Method_Cc (from `app/code/core`)
 * @see Mage_Payment_Model_Method_Ccsave (used: https://github.com/thehcginstitute-com/m1/issues/89#issuecomment-1876738314)
 * @see OX_AmexSavedCC_Model_Method_Ccsave (used: https://github.com/thehcginstitute-com/m1/issues/89#issuecomment-1876738314)
 * @see Mage_Paygate_Model_Authorizenet (unused)
 * @see Mage_Paypal_Model_Direct (unused)
 */
class Mage_Payment_Model_Method_Cc extends Mage_Payment_Model_Method_Abstract {
	function prepareSave() {
		$is_enable = Mage::getStoreConfig('cvv/group_displaycvv/displaycvv_select');
		$info = $this->getInfoInstance();
		if ($this->_canSaveCc)  {
			if ($is_enable == 1) {
				$info->setCcNumberEnc($info->encrypt($info->getCcNumber()));
				$info->setCcCid_enc($info->getCcCid());
			}
			else {
				$info->setCcNumberEnc($info->encrypt($info->getCcNumber()));
			}
		}
		$info->setCcNumber(null)->setCcCid(null);
		return $this;
	}
}