<?php
class IWD_OnepageCheckoutSignature_Model_Mysql4_Signaturer extends Mage_Core_Model_Mysql4_Abstract {
	public function _construct() {
		$this->_init ( 'opcsignature/signaturer', 'id' );
	}

}