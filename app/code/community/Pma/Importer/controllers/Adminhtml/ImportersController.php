<?php

class Pma_Importer_Adminhtml_ImportersController extends Mage_Adminhtml_Controller_Action
{
    protected $response = array();
    
    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('importer/items');
        $this->_title($this->__('PMA'));
        return $this;
    }  
    
    public function indexAction() {
        $this->_initAction();
        
        $importerModel = Mage::getModel('importer/importer');
        $collection = $importerModel->getCollection();
        
          foreach($collection as $item){
               
               $formId =  $item->getId();
               break ;
          }
                     
         if(isset($formId)):
        
          $block_form = $this->getLayout()->createBlock('Pma_Importer_Block_Adminhtml_Settings');
           
        else:
        
          $block_form = $this->getLayout()->createBlock('core/template', 'form_setting')->setTemplate('importer/settings.phtml');
          
        endif;
        
         $this->_addContent($block_form);
        
        /*$block = $this->getLayout()->createBlock('Pma_Importer_Block_Adminhtml_Importer');
        $this->_addContent($block);*/
        
        $this->renderLayout();
    }
   
    public function saveAction()
    {
      if ($this->getRequest()->getPost()) {
          
         try {
                $postData = $this->getRequest()->getPost();
                
                if (empty($postData)) {
                 Mage::throwException($this->__('Invalid form data.'));
               }
               
                $importerModel = Mage::getModel('importer/importer');
                $collection = $importerModel->getCollection();
                
               foreach($collection as $item){
                    $formId =  $item->getId();
                     break ;
               }
                     
                    
                if(isset($formId)):
                  
                    $importerModel->load($formId)
                                  ->setWebservice_url($postData['gai_settings']['webserviceUrl'])
                                  ->setAccount_token($postData['gai_settings']['accountToken'])
                                  ->setUpdated_on(now())
                                  ->setOrders_type(serialize($postData['orders']))
                                  ->save();
                 else:
                 
                    $importerModel->setWebservice_url($postData['gai_settings']['webserviceUrl'])
                                  ->setAccount_token($postData['gai_settings']['accountToken'])
                                  ->setUpdated_on(now())
                                  ->setOrders_type(serialize($postData['orders']))
                                  ->save();
                 
                    
                 endif;
                 
                if(!isset($postData['orders'])){
                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Order Filteration not selected.'));
                }
                else {
                     Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Setting was successfully saved'));
                }
                    Mage::getSingleton('adminhtml/session')->setImporterData(false);
               
              
            
             } catch (Exception $e) {
                 Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
               }
        }
        $this->_redirect('*/*/');
    }
    
}