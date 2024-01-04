<?php
class IWD_OnepageCheckoutSignature_Model_Mysql4_Signaturer_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
	function _construct() {
		parent::_construct ();
		
		$this->_init ( 'opcsignature/signaturer' );
	}
}