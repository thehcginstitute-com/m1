<?php
# 2023-12-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "«The requested Payment Method is not available» on viewing an order paid via a deleted payment module":
# https://github.com/thehcginstitute-com/m1/issues/52
use HCG_Payment_Deleted as D;
use Mage_Payment_Helper_Data as H;
use Mage_Payment_Model_Method_Abstract as M;
# 2024-01-06
# "Port the modifications of `app/code/core/Mage/Payment/Model/Info.php` to Magento 1.9.4.5":
# https://github.com/thehcginstitute-com/m1/issues/98
final class HCG_Payment_Model_Info extends Mage_Payment_Model_Info {
	/**
	 * 2024-01-06
	 * @override
	 * @see Mage_Payment_Model_Info::getMethodInstance()
	 */
	function getMethodInstance():M {/** @var M $r */
		if ($this->hasMethodInstance()) {
			$r = $this->_getData('method_instance');
		}
		else {
			$h = Mage::helper('payment'); /** @var H $h */
			/** @var string $m */
			if (!($m = $this->getMethod())) {
				Mage::throwException($h->__('The requested Payment Method is not available.'));
			}
			;
			# 2023-12-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "«The requested Payment Method is not available» on viewing an order paid via a deleted payment module":
			# https://github.com/thehcginstitute-com/m1/issues/52
			if (!($r = $h->getMethodInstance($m))) {
				$this->setMethod(D::CODE);
				$r = $h->getMethodInstance(D::CODE); /** @var D $r */
				$r->setOriginalMethod($m);
			}
			$r->setInfoInstance($this);
			$this->setMethodInstance($r);
		}
		return $r;
	}
}
