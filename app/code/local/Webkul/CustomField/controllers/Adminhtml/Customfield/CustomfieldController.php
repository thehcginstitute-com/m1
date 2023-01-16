<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */ 
class Webkul_CustomField_Adminhtml_Customfield_CustomfieldController extends Mage_Adminhtml_Controller_Action
{
    /**
    *holds customer type id
    */
    protected $_customerTypeId;
    /**
    *holds category type id
    */
    protected $_categoryTypeId;
    /**
    *holds customer address type id
    */
    protected $_customer_addressTypeId;
    protected $_type;

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/customfield/customfield');
    }
    /**
    *it set the customer type for attributes .
    */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->_customerTypeId = Mage::getModel('eav/entity')->setType('customer')->getTypeId();
        $this->_categoryTypeId = Mage::getModel('eav/entity')->setType('catalog_category')->getTypeId();
        $this->_customer_addressTypeId = Mage::getModel('eav/entity')->setType('customer_address')->getTypeId();
        
        $this->_type = 'customer';
    }
       
    /**
    * This provide the option for the select,multiselect type etc. attributes
    */
    public function getoptionAction()
    {
        $html='';
        
        if ($this->getRequest()->getParam('data')) {
            $decision=$this->getRequest()->getParam('data')+1;
            $html.="<ul class='wk_mp_headcus wktmp'>";
            $allStores = Mage::app()->getStores();
            $storeArr = array();
                // print_r($allStores);
                foreach ($allStores as $key => $val) {
                    $storeId = Mage::app()->getStore($key)->getId();
                    $storeArr[$storeId] = Mage::app()->getStore($key)->getName();
                }
            $html.=" <li><input type='text' class='wkreq required-entry widthinput' name='option[value][option_".$decision."][0]' value='' /> </li>";
            foreach ($storeArr as $storeId => $storeName) {
                $html.=" <li><input type='text' class='widthinput' name='option[value][option_".$decision."][".$storeId."]' value='' /></li>";
            }
            $html.="<li><button type='button' value='Delete Row' title='Delete Row' class='deletecusopt button'>";
            $html.="<span><span>Delete</span></span></button></li>";
        }
        $html.="</ul>";

        $this->getResponse()->setHeader('content_type','text/html');
        $this->getResponse()->setBody($html);
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu("customfield")->_addBreadcrumb($this->__("Items Manager"), $this->__("Item Manager"));
        $this->getLayout()->getBlock("head")->setTitle($this->__("Customer Custom Registration fields"));
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()->renderLayout();
    }
    /**
    * used to edit the value of the attribute
    */
    public function editAction()
    {
        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("customfield/customfields")->load($id);
        $newmodel = Mage::getModel("customfield/customfields")->load($id);        
        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
            Mage::register("customfield_data", $model);
            if (!empty($data)) {
                $model->setData($data);
            }
            
            //register the options         
            $id   = $model['customer_attribute_id'];
            $modeleav  = Mage::getModel('eav/entity_attribute');    
            $modeleav->load($id);

            if($model->getDependableInputname()!=""){
                $dependableData  = Mage::getModel("customfield/customfields");    
                $dependableId=$dependableData->load($model->getDependableInputname(),"inputname")->getCustomerAttributeId();
                $dependableData  = Mage::getModel('eav/entity_attribute');    
                $dependableData->load($dependableId);
                $modeleav->setDependableFrontendLabel($dependableData->getFrontendLabel());
                $modeleav->setDependableInputname($dependableData->getAttributeCode());
                $modeleav->setDependableFrontendInput($dependableData->getFrontendInput());
                $modeleav->setDependableBackendType($dependableData->getBackendType());
                $modeleav->setDependableSortOrder($dependableData->getSortOrder());      
                $modeleav->setDependableNote($dependableData->getNote());      
                $modeleav->setDependableFrontendClass($dependableData->getFrontendClass());     
                $modeleav->setDependableAttributeId($dependableData->getAttributeId());                               
            }
            Mage::register("customerfield_data", $modeleav);
            
            if($newmodel->getDependableInputname()!=""){
                $newmodel->load($newmodel->getDependableInputname(),"inputname");
                $newid   = $newmodel['customer_attribute_id'];
                $newmodeleav  = Mage::getModel('eav/entity_attribute');    
                $newmodeleav->load($newid);
                Mage::register("dependable_field_option", $newmodeleav);
            } else {
                Mage::register("dependable_field_option", $modeleav);                
            }
            
            
            $this->loadLayout();
            $this->_setActiveMenu("customfield");
            $this->_addContent($this->getLayout()->createBlock("customfield/adminhtml_customfields_edit"))
                    ->_addLeft($this->getLayout()->createBlock("customfield/adminhtml_customfields_edit_tabs"));
            $this->renderLayout();
        } else {
            Mage::getSingleton("adminhtml/session")->addError($this->__("Item does not exist"));
            $this->_redirect("*/*/");
        }
    }
    /**
    * Check attribute is valid or not
    */
    public function validateAction()
    {
        $response = new Varien_Object();
        $response->setError(false);

        $attributeCode  = $this->_customerTypeId;
        $attributeId    = $this->getRequest()->getParam('attribute_id');
        switch ($attributeCode) {
            case "customer":
                $this->_entityTypeId=$this->_customerTypeId;
                break;

        }
        $attribute = Mage::getModel('eav/entity_attribute')
            ->loadByCode($this->_entityTypeId, $attributeCode);

        if ($attribute->getId() && !$attributeId) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('catalog')->__('Attribute with the same code already exists'));
            $this->_initLayoutMessages('adminhtml/session');
            $response->setError(true);
            $response->setMessage($this->getLayout()->getMessagesBlock()->getGroupedHtml());
        }

        $this->getResponse()->setBody($response->toJson());
    }

    public function newAction()
    {
        $this->_forward("edit");
    }
    /**
    *This is used for save/create attribute
    */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            if ($data['frontend_input']!="select" && $data['frontend_input']!="multiselect"){
                $options=$data['option'];
                unset($data['option']);
            }
            $usedinforms=implode(',', $data['viewinforms']);
            $data['is_visible'] = true;
            $data['inputname'] = $data['attribute_code'];
            $formarray=$data['viewinforms'];
            $data['formarray']=$formarray;
            // $formarray=array_diff($formarray,array("adminhtml_customer"));
            $store = $this->getRequest()->getParam("store");
            $storeids = "";
            for ($j=0; $j<count($store); $j++) {
                $storeids = $storeids.$store[$j].",";
            }
            $storeids = rtrim($storeids, ",");
            $data['dependable_inputname']=$data['dependable_attribute_code'];
            $data["allowed_extensions"] = rtrim($data["allowed_extensions"], ",");
            $data["selectoption"] = rtrim($data["selectoption"], ",");
            $data["dependable_allowed_extensions"] = rtrim($data["dependable_allowed_extensions"], ",");
            $data["dependable_selectoption"] = rtrim($data["dependable_selectoption"], ",");
            $model = Mage::getModel("customfield/customfield");
            
            if ($this->getRequest()->getParam('attribute_id') > 0) {
                $model->setId($this->getRequest()->getParam('attribute_id'));
                $model1 = Mage::getModel("customfield/customfields")->load($this->getRequest()->getParam("id"));
                $model1->setSortOrder($this->getRequest()->getParam("sort_order"));
                $model1->setStatus($this->getRequest()->getParam("status"));                
                $model1->setIsInSaif($this->getRequest()->getParam("is_in_saif"));                
                $model1->setIsInSemail($this->getRequest()->getParam("is_in_semail"));                                
                $model1->setViewinforms($usedinforms)->save();
            }
            $data['viewinforms']=$usedinforms;

            $model->setData($data);
            try {
                if ($model->getCreatedTime == null || $model->getUpdateTime() == null) {
                    $model->setCreatedTime(now())->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }
                $model->save();


                if (isset($data['entity_type_id']) && isset($data['attribute_code']) && $data['entity_type_id'] == '1' && $data['attribute_code']) {
                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    $eavConfig = Mage::getSingleton('eav/config');
                    $attribute = $eavConfig->getAttribute('customer', $data['attribute_code']);
                    
                    $attribute->setData('used_in_forms', $formarray);
                       
                    if ($data['frontend_input'] == 'boolean' || $data['frontend_input'] == 'dependable') {
                        $attribute->setData('source_model', 'eav/entity_attribute_source_boolean');
                    }
                    
                    $attribute->save();
                    ///** for save record in table marketplace_customer_attribute **////
                    $customerattr=Mage::getModel('customfield/customfields')->getCollection()
                        ->addFieldToFilter('customer_attribute_id', array('eq'=>$attribute->getAttributeId()))
                        ->getFirstItem();
                    
                    if (!$customerattr->getIndexId()) {
                        $data['customer_attribute_id']=$attribute->getAttributeId();
                        $data['customer_attribute_code']=$data['attribute_code'];
                        $data['viewinforms']=$usedinforms;
                        $customerattrtemp=Mage::getModel('customfield/customfields')->setData($data);
                        if ($customerattrtemp->getCreatedTime == null || $model->getUpdateTime() == null) {
                            $customerattrtemp->setCreatedTime(now())->setUpdateTime(now());
                        } else {
                            $customerattrtemp->setUpdateTime(now());
                        }
                        $customerattrtemp->save();
                    }
                    ///****///

                }
                if($data['frontend_input']=="dependable") {
                    if($data['dependable_frontend_input']=='select' || $data['dependable_frontend_input']=='multiselect'){
                        $data['option']=$options;
                    }
                    $this->saveDependableField($data);
                }

                Mage::getSingleton("adminhtml/session")->addSuccess($this->__("Item was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setFormData(false);
                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getIndexId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                return;
            }
        } else {
            Mage::getSingleton("adminhtml/session")->addError($this->__("Unable to find item to save"));
            $this->_redirect("*/*/");
        }
    }

    //method for dependable save
    public function saveDependableField($dependentdata)
    {
        $data['is_visible'] = true;
        $data['inputname'] = $dependentdata['dependable_attribute_code'];        
        $data['dependent_on'] = $dependentdata['attribute_code'];            

        //refactor data
        $data['attribute_id']= $dependentdata['dependable_attribute_id'];         
        $data['attribute_code']= $dependentdata['dependable_attribute_code']; 
        $data['frontend_label']=$dependentdata['dependable_frontend_label'];
        $data['frontend_input']=$dependentdata['dependable_frontend_input'];
        $data['note'] = $dependentdata['dependable_note'];
        $data['frontend_class'] = $dependentdata['dependable_frontend_class'];  
        if ($data['frontend_input']=="select" || $data['frontend_input']=="multiselect"){
            $data['option'] = $dependentdata['option'];
        }                        
        $data['is_user_defined'] = 1;
        $data['entity_type_id'] = 1;
        $data['attribute_set_id'] = 1;
        $data['attribute_group_id'] = 1;
        $data['is_in_saif'] = 0;
        $data['is_in_semail'] = 0;
        $data['status'] = 2;
        $data['viewinforms']= $dependentdata['viewinforms'];
        $data['formarray']=$dependentdata['formarray'];        
        // $data[''];
        
        $model = Mage::getModel("customfield/customfield");
        
        $model1 = Mage::getModel("customfield/customfields")->load($data['attribute_code'],'inputname');
        if ($model1->getId()) {          
            $model1->setSortOrder($data['sort_order']);                               
            $model1->save();
        }
        //refactor before save
        $model->setData($data);
        try {
            if ($model->getCreatedTime == null || $model->getUpdateTime() == null) {
                $model->setCreatedTime(now())->setUpdateTime(now());
            } else {
                $model->setUpdateTime(now());
            }
            $model->save();


            if (isset($data['entity_type_id']) && isset($data['attribute_code']) && $data['entity_type_id'] == '1' && $data['attribute_code']) {
                $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                $eavConfig = Mage::getSingleton('eav/config');
                $attribute = $eavConfig->getAttribute('customer', $data['attribute_code']);
                $formarray=array("adminhtml_customer");
                $attribute->setData('used_in_forms', $data['formarray']);
                    
                if ($data['frontend_input'] == 'boolean') {
                    $attribute->setData('source_model', 'eav/entity_attribute_source_boolean');
                }
                
                $attribute->save();
                ///** for save record in table marketplace_customer_attribute **////
                $customerattr=Mage::getModel('customfield/customfields')->getCollection()
                    ->addFieldToFilter('customer_attribute_id', array('eq'=>$attribute->getAttributeId()))
                    ->getFirstItem();
                
                if (!$customerattr->getIndexId()) {
                    $data['customer_attribute_id']=$attribute->getAttributeId();
                    $data['customer_attribute_code']=$data['attribute_code'];
                    $data['viewinforms']=$usedinforms;
                    $customerattrtemp=Mage::getModel('customfield/customfields')->setData($data);
                    if ($customerattrtemp->getCreatedTime == null || $model->getUpdateTime() == null) {
                        $customerattrtemp->setCreatedTime(now())->setUpdateTime(now());
                    } else {
                        $customerattrtemp->setUpdateTime(now());
                    }
                    $customerattrtemp->save();
                }
                ///****///
            }

            Mage::getSingleton("adminhtml/session")->addSuccess($this->__("Dependable Item was successfully saved"));
            Mage::getSingleton("adminhtml/session")->setFormData(false);
            if ($this->getRequest()->getParam("back")) {
                $this->_redirect("*/*/edit", array("id" => $model->getIndexId()));
                return;
            }
            $this->_redirect("*/*/");
            return;
        } catch (Exception $e) {
            Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
        }
    }

    /**
    *This function returns all the custom fields of selected store through ajax function
    *
    **/

    public function getstoreoptionAction()
    {
        $storeid = $this->getRequest()->getParam("storeid");
        $html = "";
        $data = array();
        $DOB = array();
        $customerid = $this->getRequest()->getParam("customerid");
        $customfield = Mage::getModel("customfield/customfields")->getCollection()->addFieldToFilter("status", 1)->setOrder("setorder", "ASC");
        foreach ($customfield as $record) {
            $store = explode(",", $record->getStore());
            if (in_array($storeid, $store)) {
                $collection = Mage::getModel("customfield/customfielddata")->getCollection()
                    ->addFieldToFilter("field_id", $record->getId())
                    ->addFieldToFilter("customer_id", $customerid)
                    ->addFieldToFilter("store", $storeid);
                $fieldval = "";
                $required_class = "";
                $dependable_fieldval="";
                if ($record->getReuiredfield()) {
                    $required_class = "required-entry";
                }
                foreach ($collection as $coll) {
                    $fieldval = $coll->getValue();
                    $dependable_fieldval = $coll->getDependantValue();
                }
                if ($record->getInputtype() != "dependable") {
                    if ($required_class != "") {
                        $html .= "<tr><td class='label'><label>".$record->getLabelname()."<em  class='required' >*</em></label></td><td class='value'><div class='store-scope'>";
                    } else {
                        $html .= "<tr><td class='label'><label>".$record->getLabelname()."</label></td><td class='value'><div class='store-scope'>";
                    }
                }
                if ($record->getInputtype() == "text") {
                    $html .= "<input type='".$record->getInputtype()."' class='input-text'".$required_class." ".$record->getValidationType()."' id='".$record->getInputname()."' name='".$record->getInputname()."' title='".$record->getInputname()."' value= '".$fieldval."'/>";
                }
                if ($record->getInputtype() == "textarea") {
                    $html .= "<textarea class='input-text ".$required_class." ".$record->getValidationType()."' id='".$record->getInputname()."' name='".$record->getInputname()."' title='".$record->getInputname()."'>".$fieldval."</textarea>";
                }
                if ($record->getInputtype() == "dob") {
                    $DOB[] = $record->getId();
                    $html .= "<input style='width:68%;margin-right:3px;' readonly type='".$record->getInputtype()."' class='dob_type input-text ".$required_class." ".$record->getValidationType()."' id='dob-".$record->getId()."' name='".$record->getInputname()."' title='".$record->getLabelname()."' value='".$fieldval."'/><img id='dob_trig-".$record->getId()."' class='v-middle' title='Select Date of Birth' src='".Mage::getDesign()->getSkinUrl('images/grid-cal.gif', array('_area'=>'adminhtml'))."'/>";
                }
                if ($record->getInputtype() == "select") {
                    $selectoption = explode(",", $record->getSelectoption());
                    $html .= "<select class='input-text ".$required_class." ".$record->getValidationType()."' id='".$record->getInputname()."' name='".$record->getInputname()."' title='".$record->getInputname()."'><option value=''>".$this->__(" --Please Select-- ")."</option>";
                    foreach ($selectoption as $opt) {
                        $opt = explode("=>", $opt);
                        if ($fieldval == $opt[0]) {
                            $html .= "<option selected='selected' value='".$opt[0]."'>".$opt[1]."</option>";
                        } else {
                            $html .= "<option value='".$opt[0]."'>".$opt[1]."</option>";
                        }
                    }
                    $html .= "</select>";
                }
                if ($record->getInputtype() == "multiselect") {
                    $selectoption = explode(",", $record->getSelectoption());
                    $html .= "<select class='input-text ".$required_class." ".$record->getValidationType()."' id='".$record->getInputname()."' name='".$record->getInputname()."[]' title='".$record->getInputname()."' multiple><option value=''>".$this->__(" --Please Select-- ")."</option>";
                    foreach ($selectoption as $opt) {
                        $opt = explode("=>", $opt);
                        if (in_array($opt[0], explode(",", $fieldval))) {
                            $html .= "<option selected='selected' value='".$opt[0]."'>".$opt[1]."</option>";
                        } else {
                            $html .= "<option value='".$opt[0]."'>".$opt[1]."</option>";
                        }
                    }
                    $html .= "</select>";
                }
                if ($record->getInputtype() == "radio") {
                    $selectoption = explode(",", $record->getSelectoption());
                    $html .= "<div id='".$record->getId()."' >";
                    foreach ($selectoption as $opt) {
                        $opt = explode("=>", $opt);
                        if ($opt[0] == $fieldval) {
                            $html .= "<input checked=checked type=radio class= '".$record->getValidationType()."' name='".$record->getInputname()."' value='".$opt[0]."' title='".$opt[1]."'/>".$opt[1]."<br>";
                        } else {
                            $html .= "<input type='radio' name='".$record->getInputname()."' value='".$opt[0]."' title='".$opt[1]."' />".$opt[1]."<br>";
                        }
                    }
                    $html .= "</div>";
                }
                if ($record->getInputtype() == "image") {
                    $allowedExts = array("gif", "jpeg", "jpg", "png", "bmp", "ico");
                    $temp = explode(".", $fieldval);
                    $extension = end($temp);
                    if (in_array($extension, $allowedExts)) {
                        if ($fieldval) {
                            $html .= "<img class=".$required_class." ".$record->getValidationType()." src='".Mage::getBaseUrl("media").'customfield/'.$customerid.'/'.$fieldval."' width='100px' height='70px'/><br/>";
                        }
                    } else {
                        $html .= "<a class=".$required_class." value='".$fieldval. "' target='_blank' href='".Mage::getBaseUrl("media").'customfield/'.$customerid.'/'.$fieldval."'>".$fieldval."</a><br/>";
                    }
                    $html .= "<input  class='custom_file ".$record->getValidationType()."' data-allowed=".$record->getAllowedExtensions()." type='file' id='".$record->getId()."' name='".$record->getInputname()."' title='".$this->__("Select Image")."'/><br/>";
                    $html.="<span class='required'> Allowed Extensions are ".$record->getAllowedExtensions()."</span>";
                }
                if ($record->getInputtype() == "file") {
                    if ($fieldval) {
                        $html .= "<a class=".$required_class." value='".$fieldval. "' target='_blank' href='".Mage::getBaseUrl("media").'customfield/'.$customerid.'/'.$fieldval."'>".$fieldval."</a><br/>";
                    }
                    $html .= "<input  data-allowed=".$record->getAllowedExtensions()." class='custom_file ".$record->getValidationType()."' type='file' id='".$record->getId()."' name='".$record->getInputname()."' title='".$this->__("Select File")."'/>";
                    $html.="<span class='required'> Allowed Extensions are ".$record->getAllowedExtensions()."</span>";
                }
                if ($record->getInputtype() != "dependable");
                $html .= "</div></td></tr>";
                $dependable_required_class ="";
                if ($record->getDependableReuiredfield() == 1) {
                    $dependable_required_class = "required-entry";
                }

                if ($record->getInputtype() == "dependable") {
                    if ($dependable_required_class != "") {
                        $html .= "<tr class='dependant '><td class='label'> <label>".$record->getLabelname()." <em class=required >*</em></label></td><td class='value'><div class='store-scope'>";
                    } else {
                        $html .= "<tr class='dependant'><td class='label'><label>".$record->getLabelname()."</label></td><td class='value'><div class='store-scope'>";
                    }
                /**
                *$valverify variable is used to add valverify class to only dependable fields
                * which have value "no" selected
                **/
                $valverify="";
                    if ($fieldval != 1) {
                        $valverify="valverify";
                    }
                    $html .= "<select onchange='open_dependant(this)' class='".$valverify." select input-text ".$dependable_required_class." ".$record->getValidationType()."' id='".$record->getInputname()."' name='".$record->getInputname()."' title='".$record->getInputname()."'><option value=''>".$this->__(" --Please Select-- ")."</option>";
                    if ($fieldval == 1) {
                        $html .= "<option selected='selected' value='1'>Yes</option><option value='0'>No</option>";
                    } else {
                        $html .= "<option value='1'>Yes</option><option selected='selected' value='0'>No</option>";
                    }
                    $html .= "</select></div></td></tr>";
                    $html .= "<tr class='dependant'><td class='label'><label>".$record->getDependableLabelname()."</label></td><td class='value'><div class='store-scope'>";
                    if ($record->getDependableInputtype() == "dependable_textarea") {
                        $html .= "<textarea class='clstmp input-text ".$dependable_required_class." ".$record->getValidationType()."' id='".$record->getDependableInputname()."' name='".$record->getDependableInputname()."' title='".$record->getDependableLabelname()."'>".$dependable_fieldval."</textarea>";
                    }
                    if ($record->getDependableInputtype() == "dependable_text") {
                        $html .= "<input type='".$record->getDependableInputtype()."' class='clstmp input-text ".$dependable_required_class." ".$record->getValidationType()."' id='".$record->getDependableInputname()."' name='".$record->getDependableInputname()."' title='".$record->getDependableLabelname()."' value = '".$dependable_fieldval."' />";
                    }

                    if ($record->getDependableInputtype() == "dependable_select") {
                        $dependable_selectoption = explode(",", $record->getDependableSelectoption());
                        $html .= "<select class='clstmp input-text ".$dependable_required_class." ".$record->getValidationType()."' id='".$record->getDependableInputname()."' name='".$record->getDependableInputname()."' title='".$record->getDependableLabelname()."'><option value=''>".$this->__(" --Please Select-- ")."</option>";
                        foreach ($dependable_selectoption as $opt) {
                            $opt = explode("=>", $opt);
                            if ($dependable_fieldval == $opt[0]) {
                                $html .= "<option selected='selected' value='".$opt[0]."'>".$opt[1]."</option>";
                            } else {
                                $html .= "<option value='".$opt[0]."'>".$opt[1]."</option>";
                            }
                        }
                        $html .= "</select>";
                    }
                    if ($record->getDependableInputtype() == "dependable_multiselect") {
                        $dependable_selectoption = explode(",", $record->getDependableSelectoption());
                        $html .= "<select class='clstmp input-text ".$dependable_required_class." ".$record->getValidationType()."' id='".$record->getDependableInputname()."' name='".$record->getDependableInputname()."[]' title='".$record->getDependableLabelname()."' multiple><option value=''>".$this->__(" --Please Select-- ")."</option>";
                        foreach ($dependable_selectoption as $opt) {
                            $opt = explode("=>", $opt);
                            if (in_array($opt[0], explode(",", $dependable_fieldval))) {
                                $html .= "<option selected='selected' value='".$opt[0]."'>".$opt[1]."</option>";
                            } else {
                                $html .= "<option value='".$opt[0]."'>".$opt[1]."</option>";
                            }
                        }
                        $html .= "</select>";
                    }
                    if ($record->getDependableInputtype() == "dependable_radio") {
                        $dependable_selectoption = explode(",", $record->getDependableSelectoption());
                        $html .= "<div id='".$record->getId()." ' class='".$dependable_required_class." ".$record->getValidationType()." clstmp ' >";
                        foreach ($dependable_selectoption as $opt) {
                            $opt = explode("=>", $opt);
                            if ($opt[0] == $dependable_fieldval) {
                                $html .= "<input type='radio'  name='".$record->getDependableInputname()."' value='".$opt[0]."' title='".$opt[1]."' checked=checked />".$opt[1]."<br>";
                            } else {
                                $html .= "<input  type=radio  name='".$record->getDependableInputname()."' value='".$opt[0]."' title='".$opt[1]."' />".$opt[1]."<br>";
                            }
                        }
                        $html .= "</div>";
                    }
                    if ($record->getDependableInputtype() == "dependable_image") {
                        $allowedExts = array("gif", "jpeg", "jpg", "png", "bmp", "ico");
                        $temp = explode(".", $fieldval);
                        $extension = end($temp);
                        if (in_array($extension, $allowedExts)) {
                            if ($dependable_fieldval) {
                                $html .= "<a value=".$dependable_fieldval." class='".$dependable_required_class." clstmp ' target='_blank' href='".Mage::getBaseUrl("media").'customfield/'.$customerid.'/'.$dependable_fieldval."'>".$dependable_fieldval."</a><br/>";
                            }
                        } else {
                            if ($dependable_fieldval) {
                                $html .= "<img value=".$dependable_fieldval." class='".$dependable_required_class." clstmp ' src='".Mage::getBaseUrl("media").'customfield/'.$customerid.'/'.$dependable_fieldval."' width='100px' height='70px'/><br/>";
                            }
                        }


                        
                        $html .= "<input data-allowed=".$record->getDependableAllowedExtensions()."  type='file' class='custom_file ".$record->getValidationType()."' id='".$record->getDependableInputname()."' name='".$record->getDependableInputname()."' title='".$this->__("Select Image")."'/>";
                        $html.="<span class='required'> Allowed Extensions are ".$record->getDependableAllowedExtensions()."</span>";
                    }
                    if ($record->getDependableInputtype() == "dependable_file") {
                        if ($dependable_fieldval) {
                            $html .= "<a target='_blank' href='".Mage::getBaseUrl("media").'customfield/'.$customerid.'/'.$dependable_fieldval."'>".$dependable_fieldval."</a><br/>";
                        }
                        $html .= "<input data-allowed=".$record->getDependableAllowedExtensions()." type='file' class=' custom_file clstmp ".$record->getValidationType()."' id='".$record->getDependableInputname()."' name='".$record->getDependableInputname()."' title='".$this->__("Select File")."'/>";
                        $html.="<span class='required'> Allowed Extensions are ".$record->getDependableAllowedExtensions()."</span>";
                    }
                    $html .= "</select></div></td></tr>";
                }
            }
        }
        $html .= "<script type='text/javascript'>";
        foreach ($DOB as $value) {
            $html .= 'Calendar.setup({
                inputField : "dob-'.$value.'",
                ifFormat : "%m/%d/%Y",
                button : "dob_trig-'.$value.'",
                align : "Br",
                singleClick : true
            });';
        }
        $html .= "</script>";
        $this->getResponse()->setHeader('content_type','text/html');
        $this->getResponse()->setBody($html);
    }
    /**
    *Delete individual field
    */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam("id") > 0) {
            try {
                $coldel=Mage::getModel("customfield/customfields")->load($this->getRequest()->getParam("id"));
                
                $attCodeData=$coldel->getData();
                $coldel->delete();
                $setup = new Mage_Eav_Model_Entity_Setup('core_setup');

                try {
                    $custAttr =$attCodeData['customer_attribute_code'];  // here enter your attribute name which you want to remove
                       
                        $setup->removeAttribute('customer', $custAttr);
                        $custAttr." attribute is removed";
                        $this->getResponse()->setHeader('content_type','text/html');
                        $this->getResponse()->setBody($custAttr);
                } catch (Mage_Core_Exception $e) {
                    $this->_fault('data_invalid', $e->getMessage());
                }
                Mage::getSingleton("adminhtml/session")->addSuccess($this->__("Item was successfully deleted"));
                $this->_redirect("*/*/");
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            }
        }
        $this->_redirect("*/*/");
    }
    /**
    *massaction on fields for deletion
    */
    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam("ids");
        if (!is_array($ids)) {
            Mage::getSingleton("adminhtml/session")->addError($this->__("Please select item(s)"));
        } else {
            try {
                foreach ($ids as $id) {
                    $coldel=Mage::getModel("customfield/customfields")->load($id);
                    $attCodeData=$coldel->getData();
                    $coldel->delete();
                    $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                    $custAttr =$attCodeData['customer_attribute_code'];
                    $setup->removeAttribute('customer', $custAttr);
                }
                Mage::getSingleton("adminhtml/session")->addSuccess($this->__("Total of %d record(s) were successfully deleted", count($ids)));
            } catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
            }
        }
        $this->_redirect("*/*/index");
    }
    /**
    *Executes massaction on fields for changing statuses
    */
    public function massStatusAction()
    {
        $ids = $this->getRequest()->getParam("ids");
        if (!is_array($ids)) {
            Mage::getSingleton("adminhtml/session")->addError($this->__("Please select item(s)"));
        } else {
            try {
                foreach ($ids as $id) {
                    $wkadditionalinfo = Mage::getSingleton("customfield/customfields")
                                    ->load($id)
                                    ->setStatus($this->getRequest()->getParam("status"))
                                    ->setIsMassupdate(true)
                                    ->save();
                }
                $this->_getSession()->addSuccess($this->__("Total of %d record(s) were successfully updated", count($ids)));
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect("*/*/index");
    }
    public function gridAction() {
        $this->_initAction();
        $this->getResponse()->setBody($this->getLayout()->createBlock("customfield/adminhtml_customfields_grid")->toHtml());
    }
}
