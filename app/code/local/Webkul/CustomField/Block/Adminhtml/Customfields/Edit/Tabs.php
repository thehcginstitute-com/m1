<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Customfields_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
     
    public function __construct() {
        parent::__construct();
        $this->setId("customfield_tabs");
        $this->setDestElementId("edit_form");
        $this->setTitle($this->__("Item Information"));
    }

    protected function _beforeToHtml() {
        $model = Mage::registry("customfield_data");

        $this->addTab("form_section", array(
            "label"     => $this->__("Custom Registration Fields"),
            "alt"       => $this->__("Custom Registration Fields"),
            "content"   => $this->getLayout()->createBlock("customfield/adminhtml_customfields_edit_tab_form")->toHtml(),
            "active"    => true
        ));
        
        $this->addTab('labels', array(
            'label'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'title'     => Mage::helper('catalog')->__('Manage Label / Options'),
            'content'   => $this->getLayout()->createBlock('customfield/adminhtml_customfields_edit_tab_options')->toHtml(),
        ));
        return parent::_beforeToHtml();
    }

}

