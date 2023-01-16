<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Customfields_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
	/**
    *Constructor. set button label and action
    *
    **/
    public function __construct() {
        parent::__construct();
        $this->_objectId = "id";
        $this->_blockGroup = "customfield";
        $this->_controller = "adminhtml_customfields";
		$fieldid = $this->getRequest()->getParam("index_id");
        $this->_updateButton("save","label",$this->__("Save Field"),array(
            "label" 	=> Mage::helper("adminhtml")->__("Save And Continue Edit"),
            "onclick" 	=> "saveAndContinueEdit()",
            "class" 	=> "save",
        ), -100);
        $this->_updateButton("delete","label",$this->__("Delete Field"));
        // $this->_addButton("saveandcontinue", array(
        //     "label" 	=> Mage::helper("adminhtml")->__("Save And Continue Edit"),
        //     "onclick" 	=> "saveAndContinueEdit()",
        //     "class" 	=> "save",
     //    ), -100);
    }
	/**
    *Set the header text as per the Data
    *
    **/
    public function getHeaderText() {
        if (Mage::registry("customfield_data") && Mage::registry("customfield_data")->getIndexId())
            return $this->__("Edit Field ", $this->htmlEscape(Mage::registry("customfield_data")->getTitle()));
        else
            return $this->__("Add Field");
    }

}

