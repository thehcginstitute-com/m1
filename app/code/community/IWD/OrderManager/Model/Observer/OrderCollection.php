<?php

/**
 * Class IWD_OrderManager_Model_Observer_OrderCollection
 */
class IWD_OrderManager_Model_Observer_OrderCollection
{
    /**
     * @param $observer
     */
    public function hideOrderOnFront($observer)
    {
        if ($this->isFilterHiddenOrders()) {
            $order = $observer->getOrder();
            if ($order->getData('iwd_om_status') == 1) {
                $order->setId(null);
                $order->setData(array());
            }
        }
    }

    /**
     * @param $observer
     */
    public function hideOrdersOnFront($observer)
    {
        if ($this->isFilterHiddenOrders()) {
            $collection = $observer->getOrderCollection();
            $collection->addFieldToFilter('iwd_om_status', array(array('eq' => 0), array('null' => 0)));
        }
    }

    /**
     * @return mixed
     */
    protected function isFilterHiddenOrders()
    {
        $module = Mage::app()->getRequest()->getModuleName();
        return Mage::helper('iwd_ordermanager')->isAllowHideOrders()
            && in_array($module, array('sales', 'customer'));
    }
}
