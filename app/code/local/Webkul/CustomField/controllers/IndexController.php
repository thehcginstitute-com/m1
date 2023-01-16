<?php 
 /**
 * @category   Webkul
 * @package    Webkul_CustomField
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */

require_once "Mage/Customer/controllers/AccountController.php";
class Webkul_CustomField_IndexController extends Mage_Customer_AccountController
{
    public function indexAction()
    {
        $this->loadLayout(array("default","customfield_index_index"));
        $this->getLayout()->getBlock("head")->setTitle($this->__("Customer Additional Info"));
        $this->renderLayout();
    }

    public function customfieldAction()
    {
        $this->loadLayout(array("default","customfield_index_customfield"));
        $this->getLayout()->getBlock("head")->setTitle($this->__("Account Additional Info"));
        $this->renderLayout();
    }
    /**
    *save the customer attribute value
    *
    */
    public function setattrvalAction()
    {
        $wholedata          = $this->getRequest()->getParams();
        $customerid         = Mage::getSingleton('customer/session')->getCustomer()->getId();
        $customer           = Mage::getModel('customer/customer')->load($customerid);
        $wholedata['email'] = $customer->getEmail();
        $count              = 0;
        $flag=true;
        if (isset($wholedata['remove'])) {
            foreach ($wholedata['remove'] as $key=>$val) {
                if ($val==1) {
                    $wholedata[$key]="";
                    $pathfile=Mage::getBaseDir() . "/media/customer" . $customer[$key];
                    if (file_exists($pathfile)) {
                        unlink($pathfile);
                    }
                }
            }
            unset($wholedata['remove']);
        }
        
        foreach ($wholedata as $key => $val) {
            if (is_array($val)) {
                $wholedata[$key] = implode(',', $wholedata[$key]);
            }
        }
        if (count($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if ($_FILES[$key]['tmp_name']) {
                    $attribute = Mage::getSingleton("eav/config")->getAttribute('customer', $key);
                    if ($attribute->getFrontendInput()=='image') {
                        $allowedimages='jpg,jpeg,png';
                        $allowedimageslist=explode(',', $allowedimages);
                        if (!in_array(pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION), $allowedimageslist)) {
                            $flag=false;
                            Mage::getSingleton('core/session')->addNotice(Mage::helper('customfield')->__('Only "'.$allowedimages.'" extension files allowed for "'.$attribute->getFrontendLabel().'".'));
                            continue;
                        }
                    } elseif ($attribute->getFrontendInput()=='file') {
                        $allowedfiles='zip,pdf,docx';
                        $allowedfileslist=explode(',', $allowedfiles);
                        if (!in_array(pathinfo($_FILES[$key]["name"], PATHINFO_EXTENSION), $allowedfileslist)) {
                            $flag=false;
                            Mage::getSingleton('core/session')->addNotice(Mage::helper('customfield')->__('Only "'.$allowedfiles.'" extension files allowed for "'.$attribute->getFrontendLabel().'".'));
                            continue;
                        }
                    }
                    if ($flag==false) {
                        continue;
                    }
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
                    $image_name = time() . $new_file_name;
                    // delete previous file
                    if (isset($customer[$key]) && $customer[$key]!="") {
                        $pathfile=Mage::getBaseDir() . "/media/customer" . $customer[$key];
                        if (file_exists($pathfile)) {
                            unlink($pathfile);
                        }
                    }
                    // upload new file
                    move_uploaded_file($_FILES[$key]["tmp_name"], $upload_dir . $image_name);
                    
                    $wholedata[$key] = "/customfield/" . $customerid . "/" . $image_name;
                } else {
                    if (isset($wholedata[$key]) && $wholedata[$key]!="") {
                        unset($wholedata[$key]);
                    }
                }
            }
        }
        try {
            if ($flag) {
                $customer->setData($wholedata);
                $customer->setId($customerid);
                $customer->save();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('customfield')->__('Additional information has been saved successfully.'));
            }
        } catch (exception $e) {
            // continue;
        }
        $this->_redirect('customfield/index/customfield/');
    }

    public function viewfileAction()
    {
        $helper = Mage::helper('downloadable/download');
        /* @var $helper Mage_Downloadable_Helper_Download */
        $file=$this->getRequest()->getParam('file');
        $resourceType="file";
        $resource=Mage::getBaseDir('media') . DS . 'customer'.Mage::helper('core')->urlDecode($file);
        $helper->setResource($resource, $resourceType);
        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        session_write_close();
        $helper->output();
    }
}
