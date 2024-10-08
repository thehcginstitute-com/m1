<?php

/**
 * Class IWD_OrderFlags_Model_Flags_Orders
 *
 * @method string getFlagId()
 * @method IWD_OrderFlags_Model_Flags_Orders setFlagId(string $value)
 * @method string getOrderId()
 * @method IWD_OrderFlags_Model_Flags_Orders setOrderId(string $value)
 * @method string getTypeId()
 * @method IWD_OrderFlags_Model_Flags_Orders setTypeId(string $value)
 */
class IWD_OrderFlags_Model_Flags_Orders extends Mage_Core_Model_Abstract
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init('iwd_orderflags/flags_orders');
    }

    /**
     * @param $flagId
     * @param $orderId
     * @param $flagTypeId
     * @throws Exception
     */
    function addNewRelation($flagId, $orderId, $flagTypeId)
    {
        $this->unAssignFlags($orderId, $flagTypeId);

        if ($flagId > 0) {
            $this->setFlagId($flagId)
                ->setOrderId($orderId)
                ->setTypeId($flagTypeId)
                ->save();
        }
    }

    /**
     * @param $orderId
     * @param $flagTypeId
     */
    function unAssignFlags($orderId, $flagTypeId)
    {
        $collection = $this->getCollection()
            ->addFieldToFilter('order_id', $orderId)
            ->addFieldToFilter('type_id', $flagTypeId);

        foreach ($collection as $item) {
            $item->delete();
        }
    }
}
