<?php
# 2024-04-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor `INT_DisplayCvv`": https://github.com/thehcginstitute-com/m1/issues/142
namespace INT\DisplayCvv;
use HCG\Backend\User as HU;
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
				$r->setData('Expiration Date', $this->_formatCardDate($i->getCcExpYear(), $this->getCcExpMonth()));
				$canViewBankCards = hcg_is_super_admin() || !!df_backend_user()[HU::CAN_VIEW_BANK_CARDS];
				/** @var bool $deleteAction */ /** @var string $kDelete */
				$deleteAction = df_request_o()->has($kDelete = 'deleteCVV');
				$q = new Q; /** @var Q $q */
				$q->setStoreId(1)->load($i->getOrder()->getQuoteId());
				$qp = $q->getPayment(); /** @var QP $qp */
				$cvv = $qp[$k = 'cc_cid_enc']; /** @var string $k */ /** @var string|null $cvv */
				$r->setData('Credit Card Number', $canViewBankCards && !$deleteAction
					# 2024-04-07 Dmitrii Fediuk https://upwork.com/fl/mage2pro
					# "The full bank card number is visible again (after the «Wipe CVV» button is clicked)
					# if the current URL does not contain `?deleteCVV=`":
					# https://github.com/thehcginstitute-com/m1/issues/545
					&& $cvv
						? $i->getCcNumber() : substr($i->getCcNumber(), -4)
				);
				if ($cvv && $canViewBankCards) {
					if ($deleteAction) {
						$qp->unsetData($k)->save();
					}
					else {
						$r->setData('CVV', $cvv);
						?>
						<form action='<?= df_current_url() ?>' id='<?= $kDelete ?>'>
							<button class='delete' onclick='deleteCVV()' style='margin-left:8px;'>Wipe CVV</button>
							<input name='<?= $kDelete ?>' type='hidden'/>
						</form>
						<?php
					}
				}
			}
		}
		return $r;
	}
}