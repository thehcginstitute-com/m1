<?php
# 2024-09-01 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# 1) "Refactor the `Ebizmarts_MailChimp` module": https://github.com/cabinetsbay/site/issues/524
# 2) "Improve the «Yaay! Recovered by Mailchimp's campaign» block of the backend order screen":
# https://github.com/thehcginstitute-com/m1/issues/668
class Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_View_Info_Monkey extends Mage_Core_Block_Template {
	/**
	 * @var string $campaignName
	 */
	protected $_campaignName = null;

	/**
	 * @var Mage_Sales_Model_Order $order
	 */
	protected $_order = null;

	/**
	 * @return bool
	 */
	function isReferred() {
		$order = $this->getCurrentOrder();
		$ret = false;
		if ($order->getMailchimpAbandonedcartFlag() || $order->getMailchimpCampaignId()) {
			$ret = true;
		}
		return $ret;
	}

	/**
	 * @return string
	 */
	function getCampaignId() {
		$order = $this->getCurrentOrder();
		return $order->getMailchimpCampaignId();
	}

	/**
	 * @return string
	 */
	function getCampaignName() {
		if (!$this->_campaignName) {
			$campaignId = $this->getCampaignId();
			$order = $this->getCurrentOrder();
			$storeId = $order->getStoreId();
			$helper = $this->getMailChimpHelper();

			if ($helper->isEcomSyncDataEnabled($storeId)) {
				$this->_campaignName = $helper->getMailChimpCampaignNameById($campaignId, $storeId);
			}
		}
		return $this->_campaignName;
	}

	/**
	 * @param $data
	 * @return string
	 */
	function escapeQuote($data) {return $this->getMailChimpHelper()->mcEscapeQuote($data);}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	function getMailChimpHelper() {return hcg_mc_h();}

	/**
	 * @return Mage_Sales_Model_Order
	 */
	protected function getCurrentOrder() {
		if (!$this->_order) {
			$this->_order = Mage::registry('current_order');
		}
		return $this->_order;
	}

	/**
	 * Return true if campaign data is available with the current api and list selected.
	 *
	 * @return bool
	 */
	function isDataAvailable() {
		$dataAvailable = false;
		$campaignName = $this->getCampaignName();

		if ($campaignName) {
			$dataAvailable = true;
		}

		return $dataAvailable;
	}

	/**
	 * @return string
	 * @throws Mage_Core_Model_Store_Exception
	 */
	function getStoreCodeFromOrder() {
		$helper = $this->getMailChimpHelper();
		$order = $this->getCurrentOrder();
		$storeId = $order->getStoreId();
		$storeCode = $helper->getMageApp()->getStore($storeId)->getCode();

		return $storeCode;
	}
}