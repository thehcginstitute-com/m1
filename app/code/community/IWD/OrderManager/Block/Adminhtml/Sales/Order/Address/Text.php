<?php
# 2024-03-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `IWD_OrderManager` module": https://github.com/thehcginstitute-com/m1/issues/533
final class IWD_OrderManager_Block_Adminhtml_Sales_Order_Address_Text extends Mage_Adminhtml_Block_Widget {
	/**
	 * 2024-03-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `IWD_OrderManager` module": https://github.com/thehcginstitute-com/m1/issues/533
	 * @override
	 * @see Mage_Core_Block_Abstract::__construct()
	 */
	function __construct() {
		parent::__construct();
		$this->setTemplate('iwd/ordermanager/address/text.phtml');
	}

	/**
	 * 2024-03-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * 1) "Refactor the `IWD_OrderManager` module": https://github.com/thehcginstitute-com/m1/issues/533
	 * 2) It is set by @see IWD_OrderManager_Adminhtml_Sales_AddressController::getAddressTextAfterSave()
	 * @used-by self::country()
	 * @used-by self::region()
	 * @used-by app/design/adminhtml/default/default/template/iwd/ordermanager/address/text.phtml
	 * @return array(string => string)
	 */
	function address():array {return $this['address'];}

	function country() {
		$address = $this->address();
		return Mage::getModel('directory/country')->load($address['country_id'])->getName();
	}

	/**
	 * 2024-03-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `IWD_OrderManager` module": https://github.com/thehcginstitute-com/m1/issues/533
	 */
	function region():string {
		$address = $this->address();
		return (!isset($address['region']) || empty($address['region']) ?
			Mage::getModel('directory/region')->load($address['region_id'])->getName() :
			$address['region']);
	}
}