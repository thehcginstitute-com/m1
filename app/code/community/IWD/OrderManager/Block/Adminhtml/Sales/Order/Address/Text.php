<?php
class IWD_OrderManager_Block_Adminhtml_Sales_Order_Address_Text extends Mage_Adminhtml_Block_Widget {
	function __construct()
	{
		parent::__construct();
		$this->setTemplate('iwd/ordermanager/address/text.phtml');
	}

	function getRegion()
	{
		$address = $this->getAddress();

		return (!isset($address['region']) || empty($address['region']) ?
			Mage::getModel('directory/region')->load($address['region_id'])->getName() :
			$address['region']);
	}

	function getCountry()
	{
		$address = $this->getAddress();

		return Mage::getModel('directory/country')->load($address['country_id'])->getName();
	}
}