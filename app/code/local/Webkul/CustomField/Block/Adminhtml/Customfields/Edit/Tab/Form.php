<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
class Webkul_CustomField_Block_Adminhtml_Customfields_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /*
    *varible contain type for attribute like customer.
    */
    protected $_type;
    
    protected function _prepareForm()
    {
        $model = Mage::registry("customfield_data");
        
        $form = new Varien_Data_Form();
        $_type=Mage::getModel('eav/entity')->setType('customer')->getTypeId();
        $this->setForm($form);
        $fieldset = $form->addFieldset("additionalinfo_form", array("legend" => $this->__("Item information")));

        if ($model->getId()) {
            $fieldset->addField('attribute_id', 'hidden', array(
                'name' => 'attribute_id',
            ));
        }

        $fieldset->addField("frontend_label", "text", array(
            "label"     =>  $this->__("Label"),
            "class"     =>  "required-entry",
            "required"  =>  true,
            "name"      =>  "frontend_label[]"
        ));
        if ($model->getId()) {
            $fieldset->addField("inputname", "text", array(
                "label"     =>  $this->__("Name"),
                "class"     =>  "required-entry",
                "required"  =>  true,
                "name"      =>  "attribute_code",
                'readonly' => true,
                "onkeyup"   =>  "this.value=this.value.replace(/\s/g, '')"
            ));
        } else {
            $fieldset->addField("inputname", "text", array(
                "label"     =>  $this->__("Name"),
                "class"     =>  "required-entry",
                "required"  =>  true,
                "name"      =>  "attribute_code",
                "onkeyup"   =>  "this.value=this.value.replace(/\s/g, '')"
            ));
        }

        $fieldset->addField("frontend_input", "select", array(
            "label"     =>  $this->__("Type"),
            "name"      =>  "frontend_input",
            "class"     =>  "required-entry",          
            "required"  =>  true,              
            // "onchange"  =>  "selectoptionchange(this)",
            "values"    =>  array(
                                array("value" => "","label" => $this->__("Please Select")),
                                array("value" => "text","label" => $this->__("Text")),
                                array("value" => "textarea","label" => $this->__("Text Area")),
                                array("value" => "date","label" => $this->__("Date")),
                                array("value" => "select","label" => $this->__("Dropdown")),
                                array("value" => "multiselect","label" => $this->__("Multiple Select")),
                                array("value" => "boolean","label" => $this->__("Boolean (Yes/No)")),
                                array("value" => "image","label" => $this->__("Media Image")),
                                array("value" => "file","label" => $this->__("File")),
                                array("value" => "dependable","label" => $this->__("Dependable Field"))
                            )
        ));


        $fieldset->addField('entity_type_id', 'hidden', array(
            'name' => 'entity_type_id',
            'value'=>$_type
        ));

        $fieldset->addField('is_user_defined', 'hidden', array(
            'name' => 'is_user_defined',
            'value' => 1
        ));
        
        $fieldset->addField('attribute_set_id', 'hidden', array(
            'name' => 'attribute_set_id',
            'value'=>$_type
        ));
        
        $fieldset->addField('attribute_group_id', 'hidden', array(
            'name' => 'attribute_group_id',
            'value'=>$_type
        ));
        

        $fieldset->addField("sort_order", "text", array(
            "label"     =>  $this->__("Sort Order"),
            "class"     =>  "required-entry",
            "required"  =>  true,
            "name"      =>  "sort_order"
        ));

        $fieldset->addField("note", "select", array(
            "label"     =>  $this->__("Set Value Required"),
            "class"     =>  "required-entry",
            "name"      =>  "note",
            "required"  =>  true,
            "values"    =>  array(array("value" => 1, "label" => $this->__("Yes")),
                                  array("value" => 0, "label" => $this->__("No")))
        ));

        $fieldset->addField("viewinforms", "multiselect", array(
            "label"     =>  $this->__("Show Fields in."),
            "name"      =>  "viewinforms[]",
            "values"    =>  $this->getShowFieldsIn(),
            'value'   => array(3,5,7)
        ));
        $fieldset->addField('frontend_class', 'select', array(
            'name' => 'frontend_class',
            'label' => $this->__("Set Validation Type"),
            'title' => Mage::helper('catalog')->__('Data Type for Saving in Database'),
            'values' =>  array(
                array(
                    'value' => '',
                    'label' => $this->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => $this->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => $this->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => $this->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => $this->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => $this->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => $this->__('Letters(a-zA-Z) or Numbers(0-9)')
                ),
            )
        ));

        $fieldset->addField("is_in_saif", "select", array(
            "label"     =>  $this->__("Show in sales(account info field)"),
            "class"     =>  "required-entry",
            "required"  =>  true,
            "name"      =>  "is_in_saif",
            "values"    =>  array(array("value" => 0, "label" => $this->__("No")),
                                  array("value" => 1, "label" => $this->__("Yes")))
        ));

        $fieldset->addField("is_in_semail", "select", array(
            "label"     =>  $this->__("Show in Sales Email"),
            "class"     =>  "required-entry",
            "required"  =>  true,
            "name"      =>  "is_in_semail",
            "values"    =>  array(array("value" => 0, "label" => $this->__("No")),
                                  array("value" => 1, "label" => $this->__("Yes")))
        ));

        $fieldset->addField("status", "select", array(
            "label"     =>  $this->__("Status"),
            "class"     =>  "required-entry",
            "name"      =>  "status",
            "values"    =>  array(array("value" => 1, "label" => $this->__("Enabled")),
                                 array("value" => 2, "label" => $this->__("Disabled")))
        ));


        //dependable field
        $fieldset = $form->addFieldset('dependable_fieldset',
            array('legend'=>$this->__('Dependable Fields Properties')));
        
        if ($model->getId()) {
            $fieldset->addField('dependable_attribute_id', 'hidden', array(
                'name' => 'dependable_attribute_id',
            ));
        }
        $fieldset->addField('dependable_frontend_label', 'text', array(
            'name'  => 'dependable_frontend_label[]',
            'label' => $this->__('Dependable Field Label'),
            'title' => $this->__('Dependable Field Label'),
            "class" =>  "required-entry wkdependablereq",
            "required"  =>  true,
        ));
        
        if ($model->getId()) {
            $fieldset->addField('dependable_inputname', 'text', array(
                'name'  => 'dependable_attribute_code',
                'label' => $this->__('Dependable Field Name'),
                'title' => $this->__('Dependable Field Name'),
                "class" =>  "required-entry wkdependablereq",
                "required"  =>  true,
                "readonly"  => true,
                "onkeyup"   =>  "this.value=this.value.replace(/\s/g, '')"              
            ));
        } else {
            $fieldset->addField('dependable_inputname', 'text', array(
                'name'  => 'dependable_attribute_code',
                'label' => $this->__('Dependable Field Name'),
                'title' => $this->__('Dependable Field Name'),
                "class" =>  "required-entry wkdependablereq",
                "required"  =>  true,
                "onkeyup"   =>  "this.value=this.value.replace(/\s/g, '')"              
            ));
        }
        $fieldset->addField("dependable_frontend_input", "select", array(
            "label"     =>  $this->__("Dependable Field Type"),
            "name"      =>  "dependable_frontend_input",
            "class"     =>  "required-entry wkdependablereq",          
            "required"  =>  true,              
            // "onchange"  =>  "selectoptionchange(this)",
            "values"    =>  array(
                                array("value" => "","label" => $this->__("Please Select")),
                                array("value" => "text","label" => $this->__("Text")),
                                array("value" => "textarea","label" => $this->__("Text Area")),
                                array("value" => "date","label" => $this->__("Date")),
                                array("value" => "select","label" => $this->__("Dropdown")),
                                array("value" => "multiselect","label" => $this->__("Multiple Select")),
                                array("value" => "boolean","label" => $this->__("Boolean (Yes/No)")),
                                array("value" => "image","label" => $this->__("Media Image")),
                                array("value" => "file","label" => $this->__("File")),
                            )
        ));
        
        $fieldset->addField("dependable_note", "select", array(
            "label"     =>  $this->__("Dependable Field Set Value Required"),
            "class"     =>  "required-entry wkdependablereq",
            "name"      =>  "dependable_note",
            "required"  =>  true,
            "values"    =>  array(array("value" => 1, "label" => $this->__("Yes")),
                                  array("value" => 0, "label" => $this->__("No")))
        ));
        $fieldset->addField('dependable_frontend_class', 'select', array(
            'name' => 'dependable_frontend_class',
            'label' => $this->__("Dependable Field Set Validation Type"),
            'title' => Mage::helper('catalog')->__('Data Type for Saving in Database'),
            'values' =>  array(
                array(
                    'value' => '',
                    'label' => $this->__('None')
                ),
                array(
                    'value' => 'validate-number',
                    'label' => $this->__('Decimal Number')
                ),
                array(
                    'value' => 'validate-digits',
                    'label' => $this->__('Integer Number')
                ),
                array(
                    'value' => 'validate-email',
                    'label' => $this->__('Email')
                ),
                array(
                    'value' => 'validate-url',
                    'label' => $this->__('Url')
                ),
                array(
                    'value' => 'validate-alpha',
                    'label' => $this->__('Letters')
                ),
                array(
                    'value' => 'validate-alphanum',
                    'label' => $this->__('Letters(a-zA-Z) or Numbers(0-9)')
                ),
            )
        ));



        if (Mage::getSingleton("adminhtml/session")->getCustomfieldData()) {
            $form->setValues(Mage::getSingleton("adminhtml/session")->getCustomfieldData());
            Mage::getSingleton("adminhtml/session")->setCustomfieldData(null);
        } elseif (count(Mage::registry("customfield_data")->getData())) {
            $formtmp=Mage::registry("customfield_data")->getData();
            $formtmp['attribute_id']=$formtmp['customer_attribute_id'];
            $formData=Mage::registry('customerfield_data')->getData() + $formtmp;
                
            if ($formData['backend_type']=='int') {
                $formData['frontend_input']='boolean';
                if($formData['dependable_inputname']!=""){
                    $formData['frontend_input']='dependable';                    
                }
            }
            if ($formData['dependable_backend_type']=='int') {
                $formData['dependable_frontend_input']='boolean';
            }
            
            $form->setValues($formData);
        }
        return parent::_prepareForm();
    }

    /**
    *@return Where the created fields will show
    *
    */
    public function getShowFieldsIn()
    {
        $show_fields=array();
        $show_fields= array(
            array(
                'label'=>'Customer create',
                "value" =>'customer_account_edit'
                ),
            array(
                "label" => 'Customer edit',
                "value" =>'customer_account_create'),
            array(
                "label" => 'Checkout Register',
                "value"=>'checkout_register'),
            array(
                "label" => 'Adminhtml customer',
                "value"=>'adminhtml_customer'));
        return $show_fields;
    }

    public function getValidations()
    {
        $validation_array[] = array("value" => "","label" => "Please Select");
        $validation_array[] = array("value" => "validate-number","label" => "valid number");
        $validation_array[] = array("value" => "validate-digits","label" => "use numbers only, avoid spaces or other characters such as dots or comma");
        $validation_array[] = array("value" => "validate-alpha","label" => "letters only (a-z or A-Z)");
        $validation_array[] = array("value" => "validate-code","label" => "use only letters (a-z), numbers (0-9) or underscore(_)");
        $validation_array[] = array("value" => "validate-alphanum","label" => "use only letters (a-z or A-Z) or numbers (0-9)");
        $validation_array[] = array("value" => "validate-phoneStrict","label" => "use valid phone number. For example (123) 456-7890 or 123-456-7890");
        $validation_array[] = array("value" => "validate-email","label" => "email address. For example abc@example.com");
        $validation_array[] = array("value" => "validate-date","label" => "use valid date");
        $validation_array[] = array("value" => "validate-url","label" => "valid URL. http:// is required");
        $validation_array[] = array("value" => "validate-clean-url","label" => "valid URL. For example http://www.example.com or www.example.com");
        $validation_array[] = array("value" => "validate-zip","label" => "valid zip code. For example 90602 or 90602-1234");
        return $validation_array;
    }
}
