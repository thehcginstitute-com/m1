<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Block_Adminhtml_Customfields_Edit_Form extends Mage_Adminhtml_Block_Widget_Form    {
    /*
    *Set the form  and form attributes
    */
    protected function _prepareForm()      {
        $form = new Varien_Data_Form(array(
            "id"        => "edit_form",
            "action"    => $this->getUrl("*/*/save", array("id" => $this->getRequest()->getParam("id"))),
            "method"    => "post",
            "enctype"   => "multipart/form-data")
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }

}
