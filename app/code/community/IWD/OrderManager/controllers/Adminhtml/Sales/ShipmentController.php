<?php

/**
 * Class IWD_OrderManager_Adminhtml_Sales_ShipmentController
 */
class IWD_OrderManager_Adminhtml_Sales_ShipmentController extends Mage_Adminhtml_Sales_ShipmentController
{
    /**
     * @return void
     */
    function deleteAction()
    {
        if (Mage::getModel('iwd_ordermanager/shipment')->isAllowDeleteShipments()) {
            $checkedShipments = $this->getRequest()->getParam('shipment_ids');
            if (!is_array($checkedShipments)) {
                $checkedShipments = array($checkedShipments);
            }

            try {
                foreach ($checkedShipments as $shipmentId) {
                    /**
                     * @var $shipmentModel IWD_OrderManager_Model_Shipment
                     */
                    $shipmentModel = Mage::getModel('iwd_ordermanager/shipment');

                    /**
                     * @var $shipment IWD_OrderManager_Model_Shipment
                     */
                    $shipment = $shipmentModel->load($shipmentId);
                    if ($shipment->getId()) {
                        $shipment->deleteShipment();
                    } else {
                        $shipmentModel->deleteFromGrid($shipmentId);
                        Mage::getSingleton('iwd_ordermanager/logger')->itemDeleteSuccess('shipment', $shipmentId);
                    }
                }

                Mage::getSingleton('iwd_ordermanager/report')->AggregateSales();
                Mage::getSingleton('iwd_ordermanager/logger')->addMessageToPage();
            } catch (Exception $e) {
                IWD_OrderManager_Model_Logger::log($e->getMessage(), true);
                $this->_redirect('*/*/');
                return;
            }
        } else {
            $this->_getSession()->addError($this->__('This feature was deactivated.'));
            $this->_redirect('*/*/');
            return;
        }

        $this->redirect();
    }

    /**
     * Set redirect into response
     */
    protected function redirect()
    {
        $orderId = $this->getRequest()->getParam('order_id', null);
        if (empty($orderId)) {
            $this->_redirect('*/*/index');
        } else {
            $this->_redirect('*/sales_order/view', array('order_id' => $orderId));
        }
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/shipment/actions/delete');
    }
}