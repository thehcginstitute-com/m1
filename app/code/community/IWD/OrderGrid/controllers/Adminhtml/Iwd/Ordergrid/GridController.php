<?php

/**
 * Class IWD_OrderGrid_Adminhtml_Iwd_Ordergrid_GridController
 */
class IWD_OrderGrid_Adminhtml_Iwd_Ordergrid_GridController extends Mage_Adminhtml_Controller_Action
{
    /**
     * @return void
     */
    function orderCommentsAction()
    {
        try {
            $checkedOrders = $this->getCheckedOrderIds();

            $comment = $this->getRequest()->getParam('iwd_om_comment', '');
            $isCustomerNotified = $this->getRequest()->getParam('iwd_om_notified', false);
            $isVisibleOnFront = $this->getRequest()->getParam('iwd_om_visible', false);

            foreach ($checkedOrders as $orderId) {
                $order = Mage::getModel('sales/order')->load($orderId);
                $order->addStatusHistoryComment($comment)
                    ->setIsCustomerNotified($isCustomerNotified)
                    ->setIsVisibleOnFront($isVisibleOnFront);
                $order->save();
                $order->sendOrderUpdateEmail($isCustomerNotified, $comment);
            }

            $this->_getSession()->addSuccess($this->__('Comment was added to order(s)'));
        } catch (Exception $e) {
            IWD_OrderGrid_Model_Logger::log($e->getMessage());
            $this->_getSession()->addError($this->__('An error during hide order. %s', $e->getMessage()));
            $this->_redirect("*/sales_order/index");
            return;
        }

        $this->_redirect("*/sales_order/index");
    }

    /**
     * @return void
     */
    function orderedItemsAction()
    {
        $result = array('status' => 1);

        try {
            $orderId = $this->getRequest()->getPost('order_id');
            $ordered = Mage::getModel('sales/order')->load($orderId)->getItemsCollection();

            $result['table'] = $this->getLayout()
                ->createBlock('iwd_ordergrid/adminhtml_sales_order_grid_ordereditems')
                ->setData('ordered', $ordered)
                ->setData('order_id', $orderId)
                ->toHtml();
        } catch (Exception $e) {
            IWD_OrderGrid_Model_Logger::log($e->getMessage());
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->prepareResponse($result);
    }

    /**
     * @return void
     */
    function productItemsAction()
    {
        $result = array('status' => 1);

        try {
            $orderId = $this->getRequest()->getPost('order_id');
            $ordered = Mage::getModel('sales/order')->load($orderId)->getItemsCollection();

            $products = array();
            foreach ($ordered as $item) {
                $productId = $item->getProductId();
                $products[$productId] = Mage::getModel('catalog/product')->load($productId);
            }

            $result['table'] = $this->getLayout()
                ->createBlock('iwd_ordergrid/adminhtml_sales_order_grid_productitems')
                ->setData('products', $products)
                ->setData('order_id', $orderId)
                ->toHtml();
        } catch (Exception $e) {
            IWD_OrderGrid_Model_Logger::log($e->getMessage());
            $result = array('status' => 0, 'error' => $e->getMessage());
        }

        $this->prepareResponse($result);
    }

    /**
     * @return array|mixed
     */
    protected function getCheckedOrderIds()
    {
        $checkedOrders = $this->getRequest()->getParam('order_ids');
        if (!is_array($checkedOrders)) {
            $checkedOrders = array($checkedOrders);
        }

        return $checkedOrders;
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
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}