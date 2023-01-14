<?php
class Excellence_Pay_Block_Info_Pay extends Mage_Payment_Block_Info
{
	protected function _prepareSpecificInformation($transport = null)
	{
		if (null !== $this->_paymentSpecificInformation) {
			return $this->_paymentSpecificInformation;
		}
		$info = $this->getInfo();
		$transport = new Varien_Object();
		$transport = parent::_prepareSpecificInformation($transport);
		$transport->addData(array(
			Mage::helper('payment')->__('Routing Number') => $info->getCheckNo(),
			Mage::helper('payment')->__('Acount Number') => $info->getCheckDate(),
			Mage::helper('payment')->__('Bank Name') => $info->getBankName(),
			Mage::helper('payment')->__('Account Holder Name') => $info->getAccountHolderName()
		));
		return $transport;
	}
}