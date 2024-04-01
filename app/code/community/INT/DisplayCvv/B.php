<?php
# 2024-04-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `INT_DisplayCvv`": https://github.com/thehcginstitute-com/m1/issues/142
namespace INT\DisplayCvv;
use Mage_Payment_Model_Info as I;
use Mage_Sales_Model_Order_Payment as OP;
use Mage_Sales_Model_Quote as Q;
use Mage_Sales_Model_Quote_Payment as QP;
use Varien_Object as VO;
final class B extends \Mage_Payment_Block_Info_Ccsave {
	/**
	 * 2024-04-01
	 * @override
	 * @see Mage_Core_Block_Abstract::__construct()
	 */
	function __construct() {
		$this->_addButtonLabel = 'Add Item';
		$this->_blockGroup = 'displaycvv';
		$this->_controller = 'adminhtml_displaycvv';
		$this->_headerText = 'Item Manager';
		parent::__construct();
	}

	/**
	 * 2024-04-01
	 * @override
	 * @see \Mage_Payment_Block_Info_Ccsave::_prepareSpecificInformation()
	 * @used-by \Mage_Payment_Block_Info::getSpecificInformation()
	 * @param VO|null $notUsed [optional]
	 */
	protected function _prepareSpecificInformation($notUsed = null):VO {/** @var VO $r */
		if (!($r = $this->_paymentSpecificInformation)) {
			$i = $this->getInfo(); /** @var I|OP $i */
			$r = parent::_prepareSpecificInformation(new VO(['Name on the Card' => $i->getCcOwner()]));
			if (!$this->getIsSecureMode()) {
				$q = new Q; /** @var Q $q */
				$q->setStoreId(1)->load($i->getOrder()->getQuoteId());
				$qp = $q->getPayment(); /** @var QP $qp */
				$cvv = $qp[$k = 'cc_cid_enc']; /** @var string $k */ /** @var string|null $cvv */
				$cardNumberShow = substr($i->getCcNumber(), -4);
				if ($deleted = df_request_o()->has($rDeleteCVV = 'deleteCVV')) {
					$qp->unsetData($k)->save();
				}
				if (!$cvv || $deleted) {
					$r->addData([
						'Expiration Date' => $this->_formatCardDate($i->getCcExpYear(), $this->getCcExpMonth()),
						'Credit Card Number' => $cardNumberShow,
					]);
				}
				else {
					?>
					<form action='<?= df_current_url() ?>' id='<?= $rDeleteCVV ?>'>
						<button class='delete' onclick='deleteCVV()' style='margin-left:8px;'>Wipe CVV</button>
						<input name='<?= $rDeleteCVV ?>' type='hidden'/>
					</form>
					<script>
						function deleteCVV() {
							const form = document.getElementById('<?= $rDeleteCVV ?>');
							if (confirm("Are you sure you want to clear CVV Number for this order?") == true) {
								form.submit();
							}
							else {
								form.onsubmit = () => false;
							}
						}
					</script>
					<?php
					$r->addData([
						'Expiration Date' => $this->_formatCardDate($i->getCcExpYear(), $this->getCcExpMonth())
						,'Credit Card Number' => $i->getCcNumber()
						,'CVV Number' => $cvv
					]);
				}
			}
		}
		return $r;
	}
}