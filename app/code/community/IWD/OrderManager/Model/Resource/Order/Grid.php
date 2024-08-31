<?php
class IWD_OrderManager_Model_Resource_Order_Grid extends Varien_Data_Collection
{
    private $collection;

    function setMyCollection($collection)
    {
        $this->collection = $collection;
    }

    function getSize()
    {
        return $this->collection->getSize();
    }
}
