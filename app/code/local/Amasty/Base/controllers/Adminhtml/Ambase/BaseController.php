<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_Base
 */

class Amasty_Base_Adminhtml_Ambase_BaseController extends Mage_Adminhtml_Controller_Action
{
    protected $_moduleHelper;
    
    protected function _getModuleHelper($code)
    {
        if (!$this->_moduleHelper)
        {
            $this->_moduleHelper = Mage::helper("ambase/module")->init($code);
        }
        
        return $this->_moduleHelper;
    }
    
	# 2024-02-18 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Prevent Amasty from spamming the Magento's backend": https://github.com/thehcginstitute-com/m1/issues/380
    
    public function ajaxAction()
    {
        $helper = Mage::helper("ambase");
        $this->getResponse()->setBody($helper->ajaxHtml());
    }
    
    public function fixAction()
    {
        $object = Mage::app()->getRequest()->getParam('object');
        $module = Mage::app()->getRequest()->getParam('module');
        $rewrite = Mage::app()->getRequest()->getParam('rewrite');
        if ($module && $rewrite && $object){
            
            try {
                $conflict = Mage::getModel("ambase/conflict");
                $conflict->fix($object, $module, $rewrite);
                
                foreach($conflict->log() as $m)
                    Mage::getSingleton('adminhtml/session')->addNotice($m);
                        
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            
            
            $this->_redirect("adminhtml/system_config/edit", array(
                "section" => "ambase",
                "autoload" => 1
            ));
        }
    }
    
    public function rollbackAction()
    {
        $object = Mage::app()->getRequest()->getParam('object');
        $module = Mage::app()->getRequest()->getParam('module');
        $rewrite = Mage::app()->getRequest()->getParam('rewrite');
        if ($module && $rewrite && $object){
            try {
                $conflict = Mage::getModel("ambase/conflict");
                $conflict->rollback($object, $module, $rewrite);
                
                foreach($conflict->log() as $m)
                    Mage::getSingleton('adminhtml/session')->addNotice($m);
                
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
                        
            $this->_redirect("adminhtml/system_config/edit", array(
                "section" => "ambase",
                "autoload" => 1
            ));
        }
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/config');
    }
}  
