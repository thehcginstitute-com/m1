<?php

class IWD_OrderManager_Model_Order_Api_Info extends IWD_OrderManager_Model_Order_Info
{
    function init($params)
    {
        parent::init($params);
        return $this;
    }

    function getLogger()
    {
        return Mage::getSingleton('iwd_ordermanager/api_logger');
    }
}