<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Helper_Data extends Mage_Core_Helper_Abstract{
	/**
	* value to check if terms and condition functionality is enabled or not.
	*@return boolean
	*/
	public function getEnableDisable(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/enable',$current_store);
	}
	/**
	* Terms and condition popup heading
	*@return string
	*/
	public function getHeading(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/term_n_condition_heading',$current_store);
	}
	/**
	* privacy popup heading
	*@return string
	*/
	public function getPrivacyHeading(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/privacy_heading',$current_store);
	}
	/**
	* Terms and condition popup content text
	*@return string
	*/
	public function getTermContent(){
		$current_store = Mage::app()->getStore();
		if(Mage::getStoreConfig('customfield/admin2/show_content_as',$current_store)=='is_html')
			return Mage::getStoreConfig('customfield/admin2/terms_n_condition',$current_store);
		else
			return nl2br($this->escapeHtml(Mage::getStoreConfig('customfield/admin2/terms_n_condition',$current_store)));
	}
	/**
	*popup button text
	*@return string
	*/
	public function getButtonText(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/model_button_text',$current_store);
	}
	/**
	*popup button text color
	*@return string
	*/
	public function getButtonTextColor(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/button_text_color',$current_store);
	}
	/**
	*popup button color
	*@return string
	*/
	public function getButtonColor(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/button_color',$current_store);
	}
	/**
	*popup header text color
	*@return string
	*/
	public function getHeaderTextColor(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/header_text_color',$current_store);
	}
	/**
	*popup header color
	*@return string
	*/
	public function getHeaderColor(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/header_backgroud_color',$current_store);
	}
	/**
	*popup Background color
	*@return string
	*/
	public function getBgColor(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/backgroud_color',$current_store);
	}
	/**
	* privacy popup content
	*@return string
	*/
	public function getPrivacyContent(){
		$current_store = Mage::app()->getStore();
		if(Mage::getStoreConfig('customfield/admin2/show_content_as',$current_store)=='is_html')
			return Mage::getStoreConfig('customfield/admin2/privacy_n_cookies',$current_store);
		else
			return nl2br($this->escapeHtml(Mage::getStoreConfig('customfield/admin2/privacy_n_cookies',$current_store)));
	}
	/**
	* popup content text color
	*@return string
	*/
	public function getContentTextColor(){
		$current_store = Mage::app()->getStore();
		return Mage::getStoreConfig('customfield/admin2/content_text_color',$current_store);
	}
}
