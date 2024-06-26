<?php

/**
 * MailChimp For Magento
 *
 * @category  Ebizmarts_MailChimp
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     4/29/16 3:55 PM
 * @file:     Emailcatcher.php
 */
class Ebizmarts_MailChimp_Block_Popup_Emailcatcher extends Mage_Core_Block_Template
{
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
	function getHelper($type='mailchimp')
	{
		return Mage::helper($type);
	}

	protected function _canCancel() {
		$storeId = Mage::app()->getStore()->getId();
		return df_cfg(Ebizmarts_MailChimp_Model_Config::ENABLE_POPUP, $storeId)
			&& df_cfg(Ebizmarts_MailChimp_Model_Config::POPUP_CAN_CANCEL, $storeId);
	}

	protected function _popupHeading()
	{
		$storeId = Mage::app()->getStore()->getId();

		return df_cfg(Ebizmarts_MailChimp_Model_Config::POPUP_HEADING, $storeId);
	}

	protected function _popupMessage()
	{
		$storeId = Mage::app()->getStore()->getId();

		return df_cfg(Ebizmarts_MailChimp_Model_Config::POPUP_TEXT, $storeId);
	}

	protected function _modalSubscribe()
	{
		$storeId = Mage::app()->getStore()->getId();

		return df_cfg(Ebizmarts_MailChimp_Model_Config::POPUP_SUBSCRIPTION, $storeId);
	}

	protected function _getStoreId()
	{
		return Mage::app()->getStore()->getId();
	}

	protected function _handleCookie() {
		$storeId = Mage::app()->getStore()->getId();
		$emailCookie = Mage::getModel('core/cookie')->get('email');
		$subscribeCookie = Mage::getModel('core/cookie')->get('subscribe');
		$cookieValues = explode('/', $emailCookie);
		$email = $cookieValues[0];
		$email = str_replace(' ', '+', $email);
		# 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Resolve the `Ebizmarts_MailChimp` module's issues found by IntelliJ IDEA inspections":
		# https://github.com/thehcginstitute-com/m1/issues/530
		$fName = dfa($cookieValues, 1);
		$lName = dfa($cookieValues, 2);
		if ($subscribeCookie == 'true') {
			$subscriber = df_subscriber($email);
			if (!$subscriber->getId()) {
				$subscriber = df_subscriber()->setStoreId($storeId);
				if ($fName) {
					$subscriberFname = filter_var($fName, FILTER_SANITIZE_STRING);
					$subscriber->setSubscriberFirstname($subscriberFname);
				}
				if ($lName) {
					$subscriberLname = filter_var($lName, FILTER_SANITIZE_STRING);
					$subscriber->setSubscriberLastname($subscriberLname);
				}
				$subscriber->subscribe($email);
				return 'location.reload';
			}
		}
	}
}