<?php

/**
 * Checkout subscribe checkbox block renderer
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MageMonkey
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Block_Checkout_Subscribe extends Mage_Core_Block_Template
{

	protected $_lists = array();
	protected $_info = array();
	protected $_myLists = array();
	protected $_generalList = array();
	protected $_form;
	protected $_api;
	/**
	 * @var Ebizmarts_MailChimp_Helper_Data
	 */
	protected $_helper;
	protected $_storeId;

	function __construct()
	{
		parent::__construct();
		$this->_helper = hcg_mc_h();
		$this->_storeId = Mage::app()->getStore()->getId();
	}

	/**
	 * @param $data
	 * @return string
	 */
	function escapeQuote($data)
	{
		return $this->getHelper()->mcEscapeQuote($data);
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	function getHelper($type='')
	{
		return $this->_helper;
	}

	/**
	 * Render block HTML
	 *
	 * @return string
	 */
	protected function _toHtml()
	{
		$helper = $this->_helper;
		$storeId = $this->_storeId;
		$alreadySubscribed = df_subscriber($this->getQuote())->isSubscribed();
		if ($helper->isCheckoutSubscribeEnabled($storeId) && !$alreadySubscribed) {
			return parent::_toHtml();
		} else {
			return '';
		}
	}

	/**
	 * Retrieve current quote object from session
	 *
	 * @return Mage_Sales_Model_Quote
	 */
	protected function getQuote()
	{
		return Mage::getSingleton('checkout/session')
			->getQuote();
	}

	protected function getCurrentCheckoutSubscribeValue()
	{
		return $this->_helper->getCheckoutSubscribeValue($this->_storeId);
	}

	protected function isForceHidden($currentValue = null)
	{
		if (!$currentValue) {
			$currentValue = $this->getCurrentCheckoutSubscribeValue();
		}

		return ($currentValue == Ebizmarts_MailChimp_Model_System_Config_Source_Checkoutsubscribe::FORCE_HIDDEN);
	}

	protected function isForceVisible($currentValue)
	{
		return ($currentValue == Ebizmarts_MailChimp_Model_System_Config_Source_Checkoutsubscribe::FORCE_VISIBLE);
	}

	protected function isCheckedByDefault($currentValue)
	{
		return ($currentValue == Ebizmarts_MailChimp_Model_System_Config_Source_Checkoutsubscribe::CHECKED_BY_DEFAULT);
	}

	function isForceEnabled()
	{
		$currentValue = $this->getCurrentCheckoutSubscribeValue();
		if ($this->isForceHidden($currentValue) || $this->isForceVisible($currentValue)) {
			return true;
		}

		return false;
	}

	function isChecked()
	{
		$currentValue = $this->getCurrentCheckoutSubscribeValue();
		if ($this->isCheckedByDefault($currentValue) || $this->isForceVisible($currentValue)) {
			return true;
		}

		return false;
	}

	function addToPostOnLoad()
	{
		return ($this->isChecked() || $this->isForceHidden());
	}

	/**
	 * Get list data from MC
	 *
	 * @return array
	 */
	function getGeneralList()
	{
		$storeId = $this->_storeId;
		$helper = $this->_helper;
		$listId = $helper->getGeneralList($storeId);

		return $listId;
	}
}
