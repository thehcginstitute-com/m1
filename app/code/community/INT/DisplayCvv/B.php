<?php
# 2024-04-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `INT_DisplayCvv`": https://github.com/thehcginstitute-com/m1/issues/142
namespace INT\DisplayCvv;
use Mage_Payment_Model_Info as I;
use Mage_Sales_Model_Order_Payment as OP;
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
				$qid = $i->getOrder()->getQuoteId();
				$qp = new QP; /** @var QP $qp */
				$qp->load($qid, 'quote_id');
				$cvv = $qp['cc_cid_enc'];
				$cardNumberShow = substr($i->getCcNumber(), -4);
				# 2024-01-09 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# «Undefined index: rcvv in app/code/community/INT/DisplayCvv/Block/Payment/Info/Ccsave.php on line 39»:
				# https://github.com/thehcginstitute-com/m1/issues/137
				$deleteCVV = 'deleteCVV';
				if (df_request_o()->has($deleteCVV)) {
					$qp->setData(df_clean_keys($qp->getData(), [
						'cc_cid_enc', 'cc_exp_month', 'cc_exp_year', 'cc_number_enc', 'cc_last4'
					]))->save();
				}
				if (!$cvv) {
					$r->addData([
						'Expiration Date' => $this->_formatCardDate($i->getCcExpYear(), $this->getCcExpMonth()),
						'Credit Card Number' => $cardNumberShow,
					]);
				}
				else {
					?>
					<form action='<?= df_current_url() ?>' id='<?= $deleteCVV ?>'>
						<button class='delete' onclick='deleteCVV()' style='margin-left:8px;'>Wipe CVV</button>
					</form>
					<script>
						function deleteCVV() {
							const form = document.getElementById('<?= $deleteCVV ?>');
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