<?php
class IWD_OnepageCheckoutSignature_Block_Signature extends Mage_Core_Block_Template {
	
	public function __construct() {
		
		
		//$this->setTemplate ( 'opcsignature/signature.phtml' );
	
	}
	
	function getTitle()
	{
		$title = Mage::getStoreConfig('opcsignature/general/title',Mage::app()->getStore());
		if($title!='')
			return $title;
		return "Signature";
	}
	public function canShow()
	{
		if (Mage::getStoreConfig('opcsignature/general/enabled')&& Mage::getStoreConfig('opcsignature/general/enabled_front') )
			return true;
		return false;
	}
}