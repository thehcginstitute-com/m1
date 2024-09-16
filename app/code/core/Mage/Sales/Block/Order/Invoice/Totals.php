<?php
# 2024-09-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro	
class Mage_Sales_Block_Order_Invoice_Totals extends Mage_Sales_Block_Order_Totals {
	protected $_invoice = null;

	/**
	 * @return mixed|null
	 */
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

	/**
	 * @param Mage_Sales_Model_Order_Invoice $invoice
	 * @return $this
	 */
	function setInvoice($invoice)
	{
		$this->_invoice = $invoice;
		return $this;
	}

	/**
	 * Get totals source object
	 *
	 * @return Mage_Sales_Model_Order
	 */
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
		$this->removeTotal('base_grandtotal');
		return $this;
	}
}
