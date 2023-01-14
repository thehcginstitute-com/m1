<?php

class Potato_Compressor_Adminhtml_Potato_Compressor_ImageController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('system/tools');

        $this
            ->_title($this->__('System'))
            ->_title($this->__('Tools'))
            ->_title($this->__('JS/CSS Compressor'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->_initAction();
        $this
            ->_title($this->__('Images That Require Optimization'))
        ;
        $this->renderLayout();
    }

    protected function _isAllowed()
    {
        return true;
    }
}