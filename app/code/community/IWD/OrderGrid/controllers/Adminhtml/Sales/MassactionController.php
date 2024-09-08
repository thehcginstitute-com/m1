<?php

/**
 * Class IWD_OrderGrid_Adminhtml_Sales_MassactionController
 */
class IWD_OrderGrid_Adminhtml_Sales_MassactionController extends IWD_OrderGrid_Controller_Abstract
{
    function updateAction()
    {
        try {
            $this->saveMassaction();
            $result = array('status' => 1);
        } catch (Exception $e) {
            IWD_OrderGrid_Model_Logger::log($e->getMessage());
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->prepareResponse($result);
    }

    protected function saveMassaction()
    {
        $options = $this->getRequest()->getParam('options', '{}');

        Mage::getModel('iwd_ordergrid/sales_massaction')->saveMassactionForCurrentUser($options);
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordergrid/manage_massaction');
    }
}
