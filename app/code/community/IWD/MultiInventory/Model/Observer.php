<?php

/**
 * Class IWD_MultiInventory_Model_Observer
 */
class IWD_MultiInventory_Model_Observer
{
    /**
     * Check required modules:
     *   - IWD_ALL
     */
    public function checkRequiredModules()
    {
        if (Mage::getSingleton('admin/session')->isLoggedIn()) {
            if (!Mage::getConfig()->getModuleConfig('IWD_All')->is('active', 'true')) {
                $message = 'Please setup IWD_ALL in order to finish <strong>IWD Multi Inventory</strong> installation.';
                $this->addMessage($message);
            } else {
                $version = Mage::getConfig()->getModuleConfig('IWD_All')->version;
                if (version_compare($version, "2.0.0", "<")) {
                    $message = 'Please update IWD_ALL extension because some features of <strong>IWD Multi Inventory</strong> can be not available.';
                    $this->addMessage($message);
                }
            }
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function saveInventoryData(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return;
        }

        $stocks = Mage::app()->getRequest()->getParam('product', array());
        if (!isset($stocks['stocks_data'])) {
            return;
        }

        $product = $observer->getEvent()->getProduct();
        $productId = $product->getId();
        $stocks = $stocks['stocks_data'];

        foreach ($stocks as $id => $stock) {
            $stockItem = $this->getStockItem($id, $productId);

            $stockItem->setProduct($product);
            $stockItem = $this->prepareItemForSave($stockItem, $stock);
            $stockItem->setData('stock_id', $id);
            $stockItem->setData('product_id', $productId);

            $stockItem->save();
        }
    }

    /**
     * @param $stockId
     * @param $productId
     * @return Mage_CatalogInventory_Model_Stock_Item
     */
    protected function getStockItem($stockId, $productId)
    {
        $stockItem = Mage::getModel('cataloginventory/stock_item')->getCollection()
            ->addFieldToFilter('stock_id', $stockId)
            ->addFieldToFilter('product_id', $productId);

        return $stockItem->getFirstitem();
    }

    /**
     * @param $item
     * @param $stock
     * @return mixed
     */
    protected function prepareItemForSave($item, $stock)
    {
        $item->addData($stock);

        if (!isset($stock['use_config_manage_stock']) || is_null($stock['use_config_manage_stock'])) {
            $item->setData('use_config_manage_stock', false);
        }

        if (isset($stock['min_qty']) && !is_null($stock['min_qty'])
            && (!isset($stock['use_config_min_qty']) || is_null($stock['use_config_min_qty']))
        ) {
            $item->setData('use_config_min_qty', false);
        }

        if (isset($stock['min_sale_qty']) && !is_null($stock['min_sale_qty'])
            && (!isset($stock['use_config_min_sale_qty']) || is_null($stock['use_config_min_sale_qty']))
        ) {
            $item->setData('use_config_min_sale_qty', false);
        }

        if (isset($stock['max_sale_qty']) && !is_null($stock['max_sale_qty'])
            && (!isset($stock['use_config_max_sale_qty']) || is_null($stock['use_config_max_sale_qty']))
        ) {
            $item->setData('use_config_max_sale_qty', false);
        }

        if (isset($stock['backorders']) && !is_null($stock['backorders'])
            && (!isset($stock['use_config_backorders']) || is_null($stock['use_config_backorders']))
        ) {
            $item->setData('use_config_backorders', false);
        }

        if (isset($stock['notify_stock_qty']) && !is_null($stock['notify_stock_qty'])
            && (!isset($stock['use_config_notify_stock_qty']) || is_null($stock['use_config_notify_stock_qty']))
        ) {
            $item->setData('use_config_notify_stock_qty', false);
        }

        if (isset($stock['original_inventory_qty']) && !is_null($stock['original_inventory_qty'])
            && strlen($stock['original_inventory_qty']) > 0
        ) {
            $item->setQtyCorrection($item->getQty() - $stock['original_inventory_qty']);
        }

        if (isset($stock['enable_qty_increments']) && !is_null($stock['enable_qty_increments'])
            && (!isset($stock['use_config_enable_qty_inc']) || is_null($stock['use_config_enable_qty_inc']))
        ) {
            $item->setData('use_config_enable_qty_inc', false);
        }

        if (isset($stock['qty_increments']) && !is_null($stock['qty_increments'])
            && (!isset($stock['use_config_qty_increments']) || is_null($stock['use_config_qty_increments']))
        ) {
            $item->setData('use_config_qty_increments', false);
        }

        return $item;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function placeOrder(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return;
        }

        $orderId = $observer->getEvent()->getOrder()->getId();
        Mage::getModel('iwd_multiinventory/cataloginventory_stock_order')
            ->updateStockOrder($orderId);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterCreateRefund(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return;
        }

        $creditmemo = $observer->getEvent()->getCreditmemo();

        $orderId = $creditmemo->getOrderId();
        $stockOrder = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order');
        $stockOrderItem = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order_item');

        /* update iwd_cataloginventory_stock_order */
        $assigned = $stockOrder->getQtyAssigned($orderId) - $creditmemo->getTotalQty();
        $assigned = $assigned > 0 ? $assigned : 0;
        $ordered = $stockOrder->getQtyOrdered($orderId) - $creditmemo->getTotalQty();
        $stockOrder->updateStockOrder($orderId, $assigned, $ordered);

        foreach ($creditmemo->getItemsCollection() as $item) {
            $orderItem = $item->getOrderItem();
            $stockOrderItem->updateStockOrderItemIfOneStockAssignedOnly($orderItem, $item->getQty() * -1);
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterCancelRefund(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return;
        }

        $creditmemo = $observer->getEvent()->getCreditmemo();

        $orderId = $creditmemo->getOrderId();
        $stockOrder = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order');

        /* update iwd_cataloginventory_stock_order */
        $assigned = $stockOrder->getOrderQtyAssigned($orderId);
        $ordered = $stockOrder->getQtyOrdered($orderId) + $this->getCreditmemoQty($creditmemo);

        $stockOrder->updateStockOrder($orderId, $assigned, $ordered);
    }

    /**
     * @param $creditmemo
     * @return int
     */
    protected function getCreditmemoQty($creditmemo)
    {
        $qty = 0;
        foreach ($creditmemo->getAllItems() as $item) {
            $qty += $item->getQty();
        }

        return $qty;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function cancelOrder(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return;
        }

        $stockOrder = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order');
        $orderId = $observer->getEvent()->getOrder()->getId();
        $stockOrder->updateStockOrder($orderId, 0, 0);
    }

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function cancelOrderItem(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return $this;
        }

        $item = $observer->getEvent()->getItem();
        $children = $item->getChildrenItems();

        if ($item->getId() && empty($children)) {
            $stockOrderItem = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order_item');
            $assignedItems = $stockOrderItem->getStocksForOrderItem($item->getId());

            foreach ($assignedItems as $assigned) {
                $stockItem = Mage::getModel('cataloginventory/stock_item')->getCollection()
                    ->addFieldToFilter('product_id', $assigned['product_id'])
                    ->addFieldToFilter('stock_id', $assigned['stock_id'])
                    ->getFirstItem();

                if ($stockItem) {
                    $qty = $assigned['qty_stock_assigned'] * 1.0;
                    $stockItem->addQty($qty)->save();

                    $stockOrderItem->updateStockOrderItemQty($assigned['order_item_id'], $assigned['stock_id'], 0);
                }
            }
        }

        return $this;
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function afterEditItems(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            return;
        }

        $stockOrder = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order');
        $stockOrderItem = Mage::getModel('iwd_multiinventory/cataloginventory_stock_order_item');

        $orderId = $observer->getEvent()->getOrder()->getId();
        $assigned = $stockOrderItem->getOrderQtyAssigned($orderId);
        $ordered = $stockOrderItem->getQtyOrdered($orderId);

        $stockOrder->updateStockOrder($orderId, $assigned, $ordered);
    }

    /**
     * @param $observer
     */
    public function salesOrderGridCollectionLoadBefore($observer)
    {
        if (Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            $collection = $observer->getOrderGridCollection();
            $unions = $collection->getSelect()->getPart(Zend_Db_Select::UNION);
            if (count($unions)) {
                return;
            }

            $tableNameIwdCataloginventoryStockOrder = Mage::getSingleton('core/resource')->getTableName('iwd_cataloginventory_stock_order');
            $collection->getSelect()->joinLeft(
                array('iwd_cataloginventory_stock_order' => $tableNameIwdCataloginventoryStockOrder),
                "main_table.entity_id = iwd_cataloginventory_stock_order.order_id",
                array(
                    'stock_qty_assigned' => 'qty_assigned',
                    'stock_qty_ordered' => 'qty_ordered',
                    'stock_assigned' => 'assigned',
                )
            );
        }
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function appendColumnToOrderGrid(Varien_Event_Observer $observer)
    {
        $block = $observer->getBlock();

        if (isset($block) && is_object($block)
            && $block->getType() == 'adminhtml/sales_order_grid'
            && Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()
        ) {
            /** @var $block Mage_Adminhtml_Block_Sales_Order_Grid */
            $block->addColumnAfter(
                'inventory',
                array(
                    'header' => 'Assign Stock',
                    'type' => 'options',
                    'index' => 'inventory',
                    'filter_index' => 'iwd_cataloginventory_stock_order.assigned',
                    'filter' => 'IWD_MultiInventory_Block_Adminhtml_Order_Render_Filter',
                    'renderer' => 'IWD_MultiInventory_Block_Adminhtml_Order_Render_Inventory',
                    'width' => '60px'
                ),
                'status'
            );
        }
    }
}
