<?php
class IWD_OnepageCheckoutSignature_Model_Signaturer extends Mage_Core_Model_Abstract {
	
	function _construct() {
		
		parent::_construct ();
		$this->_init ( 'opcsignature/signaturer' );
	}
}