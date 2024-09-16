<?php
class Mage_Customer_Block_Account extends Mage_Core_Block_Template {
	function __construct()
	{
		parent::__construct();
		$this->setTemplate('customer/account.phtml');
		Mage::app()->getFrontController()->getAction()->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('customer')->__('My Account'));
	}
}
