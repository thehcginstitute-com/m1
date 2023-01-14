<?php
/**
 * SyncIt Group Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SyncIt Group that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.syncitgroup.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to office@syncitgroup.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.syncitgroup.com/ for more information
 * or send an email to office@syncitgroup.com
 *
 * @category   SyncIt
 * @package    SyncIt_Frontend_Logs
 * @copyright  Copyright (c) 2015 SyncIt Group (http://www.syncitgroup.com/)
 * @license    http://www.syncitgroup.com/LICENSE-1.0.html
 */

/**
 * Frontend Logs extension
 *
 * @category   SyncIt
 * @package    SyncIt_Frontend_Logs
 * @author     SyncIt Group Dev Team <support@syncitgroup.com>
 */

class SyncIt_FrontendLogs_Adminhtml_AuthenticationController extends Mage_Adminhtml_Controller_Action {

    protected function _isAllowed() {
        return true;
    }

    protected function _initAction() {
        $this->loadLayout()->_setActiveMenu("frontendlogs/authentication")->_addBreadcrumb(Mage::helper("adminhtml")->__("Authentication Manager"),Mage::helper("adminhtml")->__("Authentication Manager"));
        return $this;
    }

    public function indexAction() {
        $this->_title($this->__("FrontendLogs"));
        $this->_title($this->__("Frontend Customer Logs Manager"));

        $this->_initAction();
        $this->renderLayout();
    }
    public function editAction() {
        $this->_title($this->__("FrontendLogs"));
        $this->_title($this->__("Authentication"));
        $this->_title($this->__("Edit Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("frontendlogs/authentication")->load($id);
        if ($model->getId()) {
            Mage::register("authentication_data", $model);
            $this->loadLayout();
            $this->_setActiveMenu("frontendlogs/authentication");
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Authentication Manager"), Mage::helper("adminhtml")->__("Authentication Manager"));
            $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Authentication Description"), Mage::helper("adminhtml")->__("Authentication Description"));
            $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock("frontendlogs/adminhtml_authentication_edit"))->_addLeft($this->getLayout()->createBlock("frontendlogs/adminhtml_authentication_edit_tabs"));
            $this->renderLayout();
        }
        else {
            Mage::getSingleton("adminhtml/session")->addError(Mage::helper("frontendlogs")->__("Item does not exist."));
            $this->_redirect("*/*/");
        }
    }

    public function newAction() {

        $this->_title($this->__("FrontendLogs"));
        $this->_title($this->__("Authentication"));
        $this->_title($this->__("New Item"));

        $id = $this->getRequest()->getParam("id");
        $model = Mage::getModel("frontendlogs/authentication")->load($id);

        $data = Mage::getSingleton("adminhtml/session")->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register("authentication_data", $model);

        $this->loadLayout();
        $this->_setActiveMenu("frontendlogs/authentication");

        $this->getLayout()->getBlock("head")->setCanLoadExtJs(true);

        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Authentication Manager"), Mage::helper("adminhtml")->__("Authentication Manager"));
        $this->_addBreadcrumb(Mage::helper("adminhtml")->__("Authentication Description"), Mage::helper("adminhtml")->__("Authentication Description"));

        $this->_addContent($this->getLayout()->createBlock("frontendlogs/adminhtml_authentication_edit"))->_addLeft($this->getLayout()->createBlock("frontendlogs/adminhtml_authentication_edit_tabs"));

        $this->renderLayout();
    }

    public function saveAction() {

        $post_data=$this->getRequest()->getPost();

        if ($post_data) {
            try {
                $model = Mage::getModel("frontendlogs/authentication")
                ->addData($post_data)
                ->setId($this->getRequest()->getParam("id"))
                ->save();

                Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Authentication was successfully saved"));
                Mage::getSingleton("adminhtml/session")->setAuthenticationData(false);

                if ($this->getRequest()->getParam("back")) {
                    $this->_redirect("*/*/edit", array("id" => $model->getId()));
                    return;
                }
                $this->_redirect("*/*/");
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                Mage::getSingleton("adminhtml/session")->setAuthenticationData($this->getRequest()->getPost());
                $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
            return;
            }
        }
        $this->_redirect("*/*/");
    }

    public function deleteAction() {
            if ($this->getRequest()->getParam("id") > 0) {
                try {
                    $model = Mage::getModel("frontendlogs/authentication");
                    $model->setId($this->getRequest()->getParam("id"))->delete();
                    Mage::getSingleton("adminhtml/session")->addSuccess(Mage::helper("adminhtml")->__("Item was successfully deleted"));
                    $this->_redirect("*/*/");
                }
                catch (Exception $e) {
                    Mage::getSingleton("adminhtml/session")->addError($e->getMessage());
                    $this->_redirect("*/*/edit", array("id" => $this->getRequest()->getParam("id")));
                }
            }
            $this->_redirect("*/*/");
    }

    /**
     * Export order grid to CSV format
     */
    public function exportCsvAction() {
        $fileName   = 'authentication.csv';
        $grid       = $this->getLayout()->createBlock('frontendlogs/adminhtml_authentication_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction() {
        $fileName   = 'authentication.xml';
        $grid       = $this->getLayout()->createBlock('frontendlogs/adminhtml_authentication_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}
