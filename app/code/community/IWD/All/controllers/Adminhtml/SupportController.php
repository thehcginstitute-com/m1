<?php

class IWD_All_Adminhtml_SupportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout()
             ->_setActiveMenu('iwdall')
             ->_title($this->__('IWD - Support tickets'));

        $this->_addBreadcrumb(
            Mage::helper('iwdall')->__('IWD Support'),
            Mage::helper('iwdall')->__('IWD Support')
        );
        $this->renderLayout();
    }

    public function sendAction()
    {
        $description = $this->getRequest()->getParam('description', false);
        $description = nl2br($description);
        $informaion = $this->getRequest()->getParam('informaion', false);
        $name = $this->getRequest()->getParam('name', false);
        $email = $this->getRequest()->getParam('email', false);
        $subject = $this->getRequest()->getParam('subject', '');
        $subject = !empty($subject) ? ' - ' . $subject : '';
        $subject = $this->getRequest()->getParam('type_of_issue', 'Support') . $subject;
        $textEmail = "<p>{$description}</p><p>{$informaion}</p>";
        Mage::helper('iwdall')->sendEmail($textEmail, $subject, $email, $name);
        $this->getResponse()->setRedirect($this->getUrl("*/*/"));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/iwdall/support');
    }
}
