<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
class Webkul_CustomField_Model_Customfield extends Mage_Eav_Model_Entity_Attribute
{
    /**
    *constructor
    */
    public function _construct()
    {
        parent::_construct();
    }
    /**
    *This function is used to set frontend type, backend type and model for the attributes of customer
    */
    protected function _beforeSave()
    {
        if ($this->getFrontendInput()=="image") {
            $this->setBackendModel('catalog/category_attribute_backend_image');
            $this->setBackendType('varchar');
        }
        
        if ($this->getFrontendInput()=="date") {
            $this->setBackendModel('eav/entity_attribute_backend_datetime');
            $this->setBackendType('datetime');
        }
        
        if ($this->getFrontendInput()=="textarea") {
            $this->setBackendType('text');
        }
        
        if ($this->getFrontendInput()=="text") {
            $this->setBackendType('varchar');
        }

        if ($this->getFrontendInput()=="file") {
            $this->setBackendType('varchar');
        }
        
        if ($this->getFrontendInput()=="radio") {
            $this->setData('source_model', 'eav/entity_attribute_source_boolean');
            $this->setBackendType('int');
            $this->setFrontendInput("boolean");
        }
        
        if (($this->getFrontendInput()=="multiselect" || $this->getFrontendInput()=="select")) {
            $this->setData('source_model', 'eav/entity_attribute_source_table');
            $this->setBackendType('varchar');
        }

        if ($this->getFrontendInput()=="boolean") {
            $this->setData('source_model', 'eav/entity_attribute_source_boolean');
            $this->setBackendType('int');
            $this->setFrontendInput("select");
        }

        if ($this->getFrontendInput()=="dependable") {
            $this->setData('source_model', 'eav/entity_attribute_source_boolean');
            $this->setBackendType('int');
            $this->setFrontendInput("select");
        }
        
        return parent::_beforeSave();
    }
}
