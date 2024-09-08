<?php
class Tatvic_Uaee_Block_Uaee extends Mage_Core_Block_Template
{
    function getAccountId()
    {
        return Mage::getStoreConfig('tatvic_uaee/general/account_id');
    }
    function isAnon()
    {
        if(Mage::getStoreConfigFlag('tatvic_uaee/support/AnonIP')){
            return true;
        }
        return false;
    }
    function isUserOptOutEnable(){
        if(Mage::getStoreConfigFlag('tatvic_uaee/support/OptOut')){
            return true;
        }
        return false;
    }
	function isActive()
    {
        if(Mage::getStoreConfigFlag('tatvic_uaee/general/enable')
            ){
                return true;
        }
        return false;
    }
	function getBrandAttr(){
		
		return Mage::getStoreConfig('tatvic_uaee/ecommerce/brand') != "" ? Mage::getStoreConfig('tatvic_uaee/ecommerce/brand') : "";
	}
    function isEcommerce()
    {
        $successPath =  Mage::getStoreConfig('tatvic_uaee/ecommerce/success_url') != "" ? Mage::getStoreConfig('tatvic_uaee/ecommerce/success_url') : '/checkout/onepage/success';
        if(Mage::getStoreConfigFlag('tatvic_uaee/general/enable')
            && strpos($this->getRequest()->getPathInfo(), $successPath) !== false){
                return true;
        }
        return false;
    }
    function isCheckout()
    {
        $checkoutPath =  Mage::getStoreConfig('tatvic_uaee/ecommerce/checkout_url') != "" ?  Mage::getStoreConfig('tatvic_uaee/ecommerce/checkout_url') : '/checkout/onepage';
        if(Mage::getStoreConfigFlag('tatvic_uaee/general/enable')
            && strpos($this->getRequest()->getPathInfo(), $checkoutPath) !== false){
            return true;
        }
        return false;
    }
    function getTransactionIdField()
    {
        return 'entity_id';
    }
    function getProduct()
    {
        return Mage::registry('current_product');
    }
    function getHomeId()
    {
        return Mage::getStoreConfig('tatvic_uaee/ecommerce/home_id') != '' ? Mage::getStoreConfig('tatvic_uaee/ecommerce/home_id') : '';
    }
}

