<?php

class IWD_OrderManager_Adminhtml_Sales_AddressController extends IWD_OrderManager_Controller_Abstract
{
	protected $reload = true;

	protected function getForm()
	{
		$result = array('status' => 1);

		$addressId = $this->getRequest()->getPost('address_id');
		$address = Mage::getModel('sales/order_address')->load($addressId);
		$isAllowed = Mage::getModel('iwd_ordermanager/address')->isAllowEditAddress();

		if ($address && $isAllowed) {
			$result['form'] = $this->getLayout()
				->createBlock('iwd_ordermanager/adminhtml_sales_order_address_form')
				->setData('address_id', $addressId)
				->setData('address', $address)
				->toHtml();
		}

		return $result;
	}

	protected function updateInfo()
	{
		$result = array('status' => 1);

		$address = $this->prepareDataBeforeSave();
		$this->isNeedReloadPage($address);

		Mage::getModel('iwd_ordermanager/address')->updateOrderAddress($address);

		$result['address'] = $this->reload ? '' : self::format($address);

		return $result;
	}

	protected function isNeedReloadPage($address)
	{
		$orderAddress = Mage::getModel('sales/order_address')->load($address['address_id']);
		$recalculate = Mage::getModel('iwd_ordermanager/address')
			->isNeedRecalculateOrderTotalAmount($address, $orderAddress);

		return $this->reload = ($recalculate || isset($address["confirm_edit"]));
	}

	protected function prepareDataBeforeSave()
	{
		$address = $this->getRequest()->getPost();

		$id = $address["address_id"];

		if (isset($address["country_id_" . $id])) {
			$address["country_id"] = $address["country_id_" . $id];
			unset($address["country_id_" . $id]);
		}

		if (isset($address["region_" . $id])) {
			$address["region"] = $address["region_" . $id];
			unset($address["region_" . $id]);
		}

		if (isset($address["region_id_" . $id])) {
			$address["region_id"] = $address["region_id_" . $id];
			unset($address["region_id_" . $id]);
		}

		if (isset($address["vat_id_" . $id])) {
			$address["vat_id"] = $address["vat_id_" . $id];
			unset($address["vat_id_" . $id]);
		}

		return $address;
	}

	protected function _isAllowed()
	{
		return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/actions/edit_address');
	}

	/**
	 * 2024-03-31 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `IWD_OrderManager` module": https://github.com/thehcginstitute-com/m1/issues/533
	 * @used-by self::updateInfo()
	 */
	private static function format(array $a):string {
		/** @return string|array */
		$v = function(string $k) use($a) {return dfa($a, $k, '');};
		return df_cc_br(
			df_cc_s($v('firstname'), $v('lastname'))
			,$v('company')
			,df_cc_br($v('street'))
			,df_ccc(', ', $v('city'), df_region_name($a), $v('postcode'))
			,df_country_ctn($v('country_id'))
			,df_kv(df_clean(['T' => $v('telephone')]))
			,$v('fax')
			,df_kv(df_clean(['Tax ID' => $v('vat_id')]))
		);
	}
}