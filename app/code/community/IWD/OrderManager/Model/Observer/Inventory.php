<?php

/**
 * Class IWD_OrderManager_Model_Observer_Cataloginventory
 */
class IWD_OrderManager_Model_Observer_Inventory
{
    /**
     * @param Varien_Event_Observer $observer
     */
    public function updateOrderStatus(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_ordermanager')->isOrderManagerEnabled()) {
            return;
        }

        $status = Mage::getStoreConfig('iwd_ordermanager/inventory/order_outofstock_status');
        if (!$status) {
            return;
        }

        $product = $observer->getEvent()->getProduct();

        $collection = Mage::getModel('sales/order_item')->getCollection()
            ->addFieldToSelect(array('product_id', 'order_id', 'qty_ordered', 'qty_shipped'))
            ->addFieldToFilter('product_id', $product->getId());

        $tableSalesFlatOrder = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');

        $collection->getSelect()->joinLeft(
            array('sales_flat_order' => $tableSalesFlatOrder),
            'main_table.order_id = sales_flat_order.entity_id',
            array('status' => 'status')
        );
        $collection->getSelect()->where("main_table.qty_ordered != main_table.qty_shipped");
        $collection->getSelect()->group('order_id');

        $outofstockAllItems = Mage::getStoreConfig('iwd_ordermanager/inventory/order_outofstock_all_items');
        foreach ($collection as $item) {
            $order = Mage::getModel('sales/order')->load($item->getOrderId());

            $isInStock = false;
            $orderItems = $order->getItemsCollection();
            foreach ($orderItems as $orderItem) {

                if ($orderItem->getProductType() === 'configurable' || $orderItem->getProductType() === 'bundle' ){
                    continue;
                }

                /** @var $orderItem Mage_Sales_Model_Order_Item */
                /** @var $stock Mage_CatalogInventory_Model_Stock_Item */
                $stock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($orderItem->getProduct());
                $isInStock = ($stock->getQty() > 0 && $stock->getIsInStock());

                if ($isInStock && $outofstockAllItems) {
                    break;
                }

                if (!$isInStock && !$outofstockAllItems) {
                    break;
                }
            }

            if ($isInStock) {
                if ($order->getStatus() == $status) {
                    $status = Mage::getModel('sales/order_status')->loadDefaultByState($order->getState())->getStatus();
                }
            }

            if ($order->getPayment() != null) {
                $order->setData('status', $status);
                $order->save();
            }
        }
    }
}