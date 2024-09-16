<?php
class Mage_Payment_Block_Info_Container extends Mage_Core_Block_Template {
	/**
	 * Add payment info block to layout
	 *
	 * @inheritDoc
	 */
	protected function _prepareLayout()
	{
		if ($info = $this->getPaymentInfo()) {
			$this->setChild(
				$this->_getInfoBlockName(),
				Mage::helper('payment')->getInfoBlock($info)
			);
		}
		return parent::_prepareLayout();
	}

	/**
	 * Retrieve info block name
	 *
	 * @return false|string
	 */
	protected function _getInfoBlockName()
	{
		if ($info = $this->getPaymentInfo()) {
			return 'payment.info.' . $info->getMethodInstance()->getCode();
		}
		return false;
	}

	/**
	 * Retrieve payment info model
	 *
	 * @return Mage_Payment_Model_Info|false
	 */
	function getPaymentInfo()
	{
		return false;
	}

	/**
	 * 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "The names of arguments in `<action method="<methodName>">` calls should match the `methodName`'s arguments":
	 * https://github.com/thehcginstitute-com/m1/issues/680
	 */
	final function createTheInfoBlockIfNoPaymentMethodChosen():void {
		if (($i = $this->getPaymentInfo()) && !$i->getMethodInstance()->getCode()) {
			$this->getChild($this->_getInfoBlockName())->setTemplate('');
		}
	}
}
