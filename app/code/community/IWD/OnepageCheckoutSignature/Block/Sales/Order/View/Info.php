<?php
	class IWD_OnepageCheckoutSignature_Block_Sales_Order_View_Info extends Mage_Adminhtml_Block_Sales_Order_View_Info
	{
		protected function _construct()
		{
			$this->setTemplate('opcsignature/sales/order/view/info.phtml');
		}
	}