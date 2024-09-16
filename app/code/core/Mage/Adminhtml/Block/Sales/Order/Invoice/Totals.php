<?php
# 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
class Mage_Adminhtml_Block_Sales_Order_Invoice_Totals extends Mage_Adminhtml_Block_Sales_Totals {
	protected $_invoice = null;

	function getInvoice()
	{
		if ($this->_invoice === null) {
			if ($this->hasData('invoice')) {
				$this->_invoice = $this->_getData('invoice');
			} elseif (Mage::registry('current_invoice')) {
				$this->_invoice = Mage::registry('current_invoice');
			} elseif ($this->getParentBlock()->getInvoice()) {
				$this->_invoice = $this->getParentBlock()->getInvoice();
			}
		}
		return $this->_invoice;
	}

	function getSource()
	{
		return $this->getInvoice();
	}

	/**
	 * Initialize order totals array
	 *
	 * @return Mage_Sales_Block_Order_Totals
	 */
	protected function _initTotals()
	{
		parent::_initTotals();
		return $this;
	}
}
