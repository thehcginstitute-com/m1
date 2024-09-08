<?php

/**
 * Class IWD_OrderGrid_Controller_Abstract
 */
class IWD_OrderGrid_Controller_Abstract extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return void
     */
    function getFormAction()
    {
        try {
            $result = $this->getForm();
        } catch (Exception $e) {
            IWD_OrderGrid_Model_Logger::log($e->getMessage());
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->prepareResponse($result);
    }

    /**
     * @return array
     */
    protected function getForm()
    {
        return array('status' => -1);
    }

    /**
     * @return array
     */
    protected function updateInfo()
    {
        return array('status' => -1);
    }

    /**
     * @return int
     */
    protected function getOrderId()
    {
        return $this->getRequest()->getPost('order_id');
    }

    /**
     * @return Mage_Sales_Model_Order
     */
    protected function getOrder()
    {
        $orderId = $this->getOrderId();
        return Mage::getModel('sales/order')->load($orderId);
    }

    /**
     * @param $result
     */
    protected function prepareResponse($result)
    {
        $this->getResponse()->setHeader('Content-type', 'application/json', true);
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin');
    }
}