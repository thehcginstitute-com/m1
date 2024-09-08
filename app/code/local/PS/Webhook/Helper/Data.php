<?php
class PS_Webhook_Helper_Data extends Mage_Core_Helper_Abstract
{
	function getDomain() {
		$baseUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$parsedUrl = parse_url($baseUrl);
		if ( array_key_exists('host', $parsedUrl)) {
			return $parsedUrl['host'];
		} else {
			return "";
		}
	}
}
