<?php
# 2024-04-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `INT_DisplayCvv`": https://github.com/thehcginstitute-com/m1/issues/142
class INT_DisplayCvv_Block_Payment_Info_Ccsave extends Mage_Payment_Block_Info_Ccsave {
	function __construct() {
		$this->_controller = 'adminhtml_displaycvv';
		$this->_blockGroup = 'displaycvv';
		$this->_headerText = 'Item Manager';
		$this->_addButtonLabel = 'Add Item';
		parent::__construct();
	}
  
	protected function _prepareSpecificInformation($transport = null) {
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$is_enable = Mage::getStoreConfig('cvv/group_displaycvv/displaycvv_select');
		$info = $this->getInfo();
		$transport = new Varien_Object(['Name on the Card' => $info->getCcOwner()]);
		$transport = parent::_prepareSpecificInformation($transport);
		if (!$this->getIsSecureMode()) {
			if ($is_enable == 1) {
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
					$transport->addData([
						'Expiration Date' => $this->_formatCardDate($info->getCcExpYear(), $this->getCcExpMonth())
						,'Credit Card Number' => $info->getCcNumber()
						,'CVV Number' => $cvv
					]);
				}
				else {
					$transport->addData(array(
						Mage::helper('payment')->__('Expiration Date') =>
						$this->_formatCardDate($info->getCcExpYear(), $this->getCcExpMonth()),
						Mage::helper('payment')->__('Credit Card Number') => $cardNumberShow,
					));
				}
			}
			else {
				$transport->addData(array(
					Mage::helper('payment')->__('Expiration Date') =>
						$this->_formatCardDate($info->getCcExpYear(), $this->getCcExpMonth())
					, Mage::helper('payment')->__('Credit Card Number') => $info->getCcNumber()
				));
			}
		}
		return $transport;
	}
}