<?php
class Mage_Sales_Block_Order_Details extends Mage_Core_Block_Template {
	function __construct() {
		parent::__construct();
		$this->setTemplate('sales/order/details.phtml');
		$this->setOrder(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id')));
		Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle('Order Details');
	}

	/**
	 * @return string
	 */
	function getBackUrl()
	{
		return Mage::getUrl('*/*/history');
	}

	/**
	 * @return mixed
	 */
	function getInvoices()
	{
		return Mage::getResourceModel('sales/invoice_collection')->setOrderFilter($this->getOrder()->getId())->load();
	}

	/**
	 * @return string
	 */
	function getPrintUrl()
	{
		return Mage::getUrl('*/*/print');
	}
}
