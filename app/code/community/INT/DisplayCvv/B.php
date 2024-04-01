<?php
# 2024-04-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `INT_DisplayCvv`": https://github.com/thehcginstitute-com/m1/issues/142
use Varien_Object as VO;
class INT_DisplayCvv_B extends Mage_Payment_Block_Info_Ccsave {
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
	 * @see Mage_Payment_Block_Info_Ccsave::_prepareSpecificInformation()
	 * @used-by Mage_Payment_Block_Info::getSpecificInformation()
	 * @param VO|null $notUsed [optional]
	 */
	protected function _prepareSpecificInformation($notUsed = null):VO {/** @var VO $r */
		if (!($r = $this->_paymentSpecificInformation)) {
			$info = $this->getInfo();
			$r = new VO(['Name on the Card' => $info->getCcOwner()]);
			$r = parent::_prepareSpecificInformation($r);
			if (!$this->getIsSecureMode()) {
				if (Mage::getStoreConfig('cvv/group_displaycvv/displaycvv_select')) {
					$order = Mage::getModel("sales/order")->load($info->getOrder()->getId());
					$payement_quote_id = $order->getQuoteId();
					$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
					$select = $connection->select()
						->from('sales_flat_quote_payment', ['*'])
						->where('quote_id=?', $payement_quote_id)
					;
					$rowArray =$connection->fetchRow($select);
					$cvv = $rowArray['cc_cid_enc'];
					$cardNumberShow = $info->getCcNumber();
					$cardNumberShow = substr($info->getCcNumber(), -4);
					$CcLast4 = $rowArray['cc_last4'];
					# 2024-01-09 Dmitrii Fediuk https://upwork.com/fl/mage2pro
					# «Undefined index: rcvv in app/code/community/INT/DisplayCvv/Block/Payment/Info/Ccsave.php on line 39»:
					# https://github.com/thehcginstitute-com/m1/issues/137
					if ($qid = df_request('rcvv')) {
						$connection = Mage::getSingleton('core/resource')->getConnection('core_read');
						$connection->update(
							"sales_flat_quote_payment"
							,["cc_cid_enc" => '',"cc_exp_month" => '',"cc_exp_year" => '',"cc_number_enc"=>'',"cc_last4"=>'']
							,"quote_id=$qid"
						);
					}
					if ($cvv !='') {
						$remove_html  = '<form method="get" id="rmvcvv" action="'.Mage::helper('core/url')->getCurrentUrl().'">
						<input type="hidden" name="rcvv" value="'.$payement_quote_id.'">
						<button class="delete" style="margin-left:8px; " onclick="removeCVV()">Wipe CVV</button>
						</form>
						';?>
						<script>
							function removeCVV() {
								if (confirm("Are you sure you want to clear CVV Number for this order?") == true) {
									document.getElementById("rmvcvv").submit();
								}
								else {
									document.getElementById('rmvcvv').onsubmit = () => false;
								}
							}
						</script>
						<?php echo $remove_html;
						$r->addData([
							'Expiration Date' => $this->_formatCardDate($info->getCcExpYear(), $this->getCcExpMonth())
							,'Credit Card Number' => $info->getCcNumber()
							,'CVV Number' => $cvv
						]);
					}
					else {
						$r->addData([
							'Expiration Date' => $this->_formatCardDate($info->getCcExpYear(), $this->getCcExpMonth()),
							'Credit Card Number' => $cardNumberShow,
						]);
					}
				}
				else {
					$r->addData([
						'Expiration Date' => $this->_formatCardDate($info->getCcExpYear(), $this->getCcExpMonth())
						,'Credit Card Number' => $info->getCcNumber()
					]);
				}
			}
		}
		return $r;
	}
}