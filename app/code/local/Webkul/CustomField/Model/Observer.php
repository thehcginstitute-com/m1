<?php
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
class Webkul_CustomField_Model_Observer
{
    /**
     *
     * set the customer attribute from billing form
     */
    public function beforeCustomerRegisterQuote(Varien_Event_Observer $observer)
    {
        if (!Mage::getSingleton('core/session')->getData('wkcustomerdata')) {
            $billingData = Mage::app()->getRequest()->getParams();
            $collection = Mage::getModel("customfield/customfields")->getCollection();
            $allAttrIds=array();
            foreach ($collection as $value) {
                $allAttrIds[]=$value->getCustomerAttributeCode();
            }
            $currentData = array();
            foreach ($billingData as  $v) {
                foreach ($allAttrIds as $key => $value) {
                    if (array_key_exists($value,$v)) {
                        $currentData[$value] = $v[$value];
                    }
                }
            }
            if (count($currentData)) {
                Mage::getSingleton('core/session')->setData('wkcustomerdata', $currentData);
            }
        }
    }
    
    /**
     *
     * set the customer attribute from billing form
     */
    public function afterOrderPlace(Varien_Event_Observer $observer)
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerData = Mage::getSingleton('customer/session')->getCustomer();
            $customerid = $customerData->getId();
            $customer_email = $customerData->getEmail();
 
            $wkcustomerData = Mage::getSingleton('core/session')->getData('wkcustomerdata');
            if (count($wkcustomerData)) {
                $customer = Mage::getModel('customer/customer')->load($customerId);
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
                $customer->loadByEmail($customer_email);
                // $customer->setId($customerId);
                foreach ($wkcustomerData as $key => $value) {
                    $customer->setData($key, $value);
                }
                try {
                    $customer->save();
                } catch (Exception $ex) {
                    Mage::getSingleton('core/session')->unsetData('wkcustomerdata');
                    Mage::throwException($ex->getMessage());
                }
                Mage::getSingleton('core/session')->unsetData('wkcustomerdata');
            }
        }
    }
    /**
    *Throws exception if terms and condition functionality is enableand while registration
    * checkbox is not checked.
    */
    public function beforeCustomerRegister(Varien_Event_Observer $observer)
    {

        $allowedFiles = explode(",",Mage::getStoreConfig('customfield/admin1/allowfiles'));
        $allowedImages = explode(",",Mage::getStoreConfig('customfield/admin1/allowimages'));
        
        if (count($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if ($_FILES[$key]['name']) {
                    $collection = Mage::getResourceModel('customer/attribute_collection')
                        ->addFilter('is_user_defined', 1)
                        ->addFilter('attribute_code',$key);
                    $field=$collection->getFirstItem();
                    if($field->getFrontendInput()=="image") {
                        $ext = pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION);
                        if(!in_array($ext,$allowedImages)){
                            Mage::throwException(Mage::helper('customer')->__('Please select Image matching allowed extentions.'));                            
                        }
                    } elseif ($field->getFrontendInput()=="file"){
                        $ext = pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION);                        
                        if(!in_array($ext,$allowedFiles)){
                            Mage::throwException(Mage::helper('customer')->__('Please select File matching allowed extentions.'));                            
                        }
                    }
                }
            }
        }
        
        if (Mage::helper('customfield')->getEnableDisable()) {
            $paramData = Mage::app()->getRequest()->getParams();
            
            if (array_key_exists('termsprivacy', $paramData)) {
                if (!in_array('on', $paramData)) {
                    Mage::throwException(Mage::helper('customer')->__('Please select Term & Condition and Privacy check.'));
                }
            }
        }
    }
    /**
    *This observer is used to save the files for file type customer attributes, while registration
    *
    */
    public function CustomerRegister($observer)
    {
        $data=Mage::getSingleton('core/app')->getRequest();
        $wholedata = $data->getParams();
        $customerid = $observer->getCustomer()->getId();
        if (count($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if ($_FILES[$key]['name']) {
                    $new_file_name = $_FILES[$key]["name"];
                    if (!is_dir(Mage::getBaseDir().'/media/customer/')) {
                        mkdir(Mage::getBaseDir().'/media/customer/', 0755);
                    }
                    if (!is_dir(Mage::getBaseDir().'/media/customer/customfield/')) {
                        mkdir(Mage::getBaseDir().'/media/customer/customfield/', 0755);
                    }
                    $upload_dir    = Mage::getBaseDir() . "/media/customer/customfield/" . $customerid . "/";

                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755);
                    }
                    $image_name = time().$new_file_name;
                    // delete previous file
                    //unlink($upload_dir.$customer[$key]);
                    // upload new file
                    move_uploaded_file($_FILES[$key]["tmp_name"], $upload_dir.$image_name);
                    // $wholedata[$key] = "/customfield/".$customerid."/".$image_name;
                }
            }
        }
        unset($wholedata['password']);
        unset($wholedata['confirmation']);
        $customer = Mage::getModel('customer/customer')->load($customerid);
        $customer->setData($wholedata);
        $customer->setId($customerid);
        $customer->save();
    }
    /**
    *This observer is used to save the files for file type customer attributes, while customer save
    *
    */
    public function afterSaveCustomer($observer)
    {
        $customer=$observer->getCustomer();
        $customerid=$customer->getId(); //die;
        $wholedata = $customer->getData();
        $customer = Mage::getModel('customer/customer')->load($customerid);
        if (count($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if ($_FILES[$key]['name']) {
                    $new_file_name = $_FILES[$key]["name"];
                    if (!is_dir(Mage::getBaseDir().'/media/customer/')) {
                        mkdir(Mage::getBaseDir().'/media/customer/', 0755);
                    }
                    if (!is_dir(Mage::getBaseDir().'/media/customer/customfield/')) {
                        mkdir(Mage::getBaseDir().'/media/customer/customfield/', 0755);
                    }
                    $upload_dir    = Mage::getBaseDir() . "/media/customer/customfield/" . $customerid . "/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777);
                    }
                    $image_name = time().$new_file_name;
                    // delete previous file
                    if (isset($customer[$key]) && $customer[$key]!="") {
                        $pathfile=Mage::getBaseDir() . "/media/customer" . $customer[$key];
                        if (file_exists($pathfile)) {
                            unlink($pathfile);
                        }
                    }
                    // upload new file
                    move_uploaded_file($_FILES[$key]["tmp_name"], $upload_dir.$image_name);
                    $wholedata[$key] = "/customfield/".$customerid."/".$image_name;
                }
            }
        }
        
        $customer->setData($wholedata);
        $customer->setId($customerid);
        $customer->save();
    }

    public function loadBeforeCustomer(Varien_Event_Observer $observer)
    {  
        $modelOld = Mage::getModel("customfield/customfielddata")->getCollection();
        if ($modelOld->getSize()) {
            foreach ($modelOld->getData() as $key => $value) {
                $oldData=array();
                $oldData['frontend_label'] = array(0=>$value['labelname']);
                $oldData['attribute_code'] = preg_replace("/[^A-Za-z0-9]/", "", $value['inputname']);
                $oldData['frontend_input'] = $value['inputtype'];
                $oldData['entity_type_id'] = 1;
                $oldData['is_user_defined'] = 1;
                $oldData['attribute_set_id'] = 1;
                $oldData['attribute_group_id'] = 1;
                $oldData['sort_order'] = $value['setorder'];
                $oldData['status'] = $value['status'];
                $oldData['is_in_saif'] = $value['is_in_saif'];
                $oldData['is_in_semail'] = $value['is_in_semail'];
                if ($value['options']) {
                    $options=array();
                    $count=0;
                    $exp=explode(',', $value['options']);
                    foreach ($exp as $key => $value) {
                        $options['value']['option_'.$count]= array(0 => $value,1 => $value);
                        $count++;
                    }
                    $oldData['option']=$options;
                }
                $oldData['note'] =0;
                $oldData['viewinforms'] = array(
                                                0 => 'customer_account_edit',
                                                1 => 'customer_account_create',
                                                2 => 'adminhtml_customer',
                                            );
                $attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode('customer', $oldData['attribute_code']);
                if (!count($attributeModel->getData())) {
                    $model = Mage::getModel("customfield/customfield");
                    $model->setData($oldData);

                    try {
                        if ($model->getCreatedTime == null || $model->getUpdateTime() == null) {
                            $model->setCreatedTime(now())->setUpdateTime(now());
                        } else {
                            $model->setUpdateTime(now());
                        }
                        $model->save();

                            
                        if (isset($oldData['entity_type_id']) && isset($oldData['attribute_code']) && $oldData['entity_type_id'] == '1' && $oldData['attribute_code']) {
                            $setup = new Mage_Eav_Model_Entity_Setup('core_setup');
                            $eavConfig = Mage::getSingleton('eav/config');
                            $attribute = $eavConfig->getAttribute('customer', $oldData['attribute_code']);
                            $attribute->setData('used_in_forms', array('customer_account_edit',
                                                             'customer_account_create',
                                                             'adminhtml_customer'));
                  
                            if ($oldData['frontend_input'] == 'boolean') {
                                $attribute->setData('source_model', 'eav/entity_attribute_source_boolean');
                            }
                                
                            $attribute->save();
                                ///** for save record in table marketplace_customer_attribute **////
                                $customerattr=Mage::getModel('customfield/customfields')->getCollection()
                                    ->addFieldToFilter('customer_attribute_id', array('eq'=>$attribute->getAttributeId()))
                                    ->getFirstItem();
                                
                            if (!$customerattr->getIndexId()) {
                                $oldData['customer_attribute_id']=$attribute->getAttributeId();
                                $oldData['customer_attribute_code']=$oldData['attribute_code'];
                                $usedinforms=implode(',', $oldData['viewinforms']);
                                $oldData['viewinforms']=$usedinforms;
                                $oldData['inputname']=$oldData['attribute_code'];
                                $customerattrtemp=Mage::getModel('customfield/customfields')->setData($oldData);
                                if ($customerattrtemp->getCreatedTime == null || $model->getUpdateTime() == null) {
                                    $customerattrtemp->setCreatedTime(now())->setUpdateTime(now());
                                } else {
                                    $customerattrtemp->setUpdateTime(now());
                                }
                                $customerattrtemp->save();
                            }
                                ///****///
                        }
                    } catch (Exception $e) {
                    }
                }
            }
        }
    }
}
