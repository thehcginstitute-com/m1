<?php

/**
 * Class IWD_SettlementReport_Adminhtml_Iwd_Settlementreport_TransactionsController
 */
class IWD_SettlementReport_Adminhtml_Iwd_Settlementreport_TransactionsController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('iwd_settlementreport')
            ->_title($this->__('IWD - Settlement Reports'));

        $this->_addBreadcrumb(
            Mage::helper('iwd_settlementreport')->__('Settlement Reports'),
            Mage::helper('iwd_settlementreport')->__('Settlement Reports')
        );

        return $this;
    }

    public function indexAction()
    {
        $connection = Mage::helper('iwd_settlementreport')->checkApiCredentials();

        if ($connection['error'] == 0) {
            $this->_showLastExecutionTime();
            $this->_initAction();
            $this->_addContent($this->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions'));
        } else {
            $this->_initAction();
            $errorBlock = $this->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions_error');
            $errorBlock->setData('message', $connection['message']);
            $this->_addContent($errorBlock);
        }

        $this->renderLayout();
    }

    public function sendreportAction()
    {
        try {
            $email = $this->getRequest()->getParam('email', null);
            if (empty($email)) {
                Mage::throwException('Email is empty.');
            }

            //Mage::getModel("iwd_settlementreport/transactions")->refresh();
            Mage::getModel('iwd_settlementreport/email_report')->sendEmail($email);

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('iwd_settlementreport')->__('The reports were successfully sent.')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('iwd_settlementreport')->__('Error: ') . $e->getMessage()
            );
        }

        $this->_redirect('*/*/');
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions_grid')->toHtml()
        );
    }

    public function updateAction()
    {
        try {
            Mage::getModel("iwd_settlementreport/transactions")->refresh();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('iwd_settlementreport')->__('Refreshed Successfully')
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('iwd_settlementreport')->__('Error: ') . $e->getMessage()
            );
        }

        $this->_redirect('*/*/');
    }

    public function exportCsvAction()
    {
        $fileName = 'transactions.csv';
        $content = $this->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions_grid')
            ->getCsvFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportExcelAction()
    {
        $fileName = 'transactions.xml';
        $content = $this->getLayout()->createBlock('iwd_settlementreport/adminhtml_transactions_grid')->getExcelFile();

        $this->_prepareDownloadResponse($fileName, $content);
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/iwdall/iwd_settlementreport');
    }

    protected function _showLastExecutionTime()
    {
        $flag = Mage::getModel('reports/flag')->setReportFlagCode('iwd_settlementreport_transactions')->loadSelf();
        $updatedAt = ($flag->hasData())
            ? Mage::app()->getLocale()
                ->storeDate(0, new Zend_Date($flag->getLastUpdate(), Varien_Date::DATETIME_INTERNAL_FORMAT), true)
            : 'undefined';

        Mage::getSingleton('adminhtml/session')
            ->addNotice(Mage::helper('adminhtml')->__('Last updated: %s.', $updatedAt));
        return $this;
    }
}