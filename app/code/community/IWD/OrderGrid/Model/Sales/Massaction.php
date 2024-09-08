<?php

/**
 * Class IWD_OrderGrid_Model_Sales_Massaction
 */
class IWD_OrderGrid_Model_Sales_Massaction extends Mage_Core_Model_Abstract
{
    /**
     * @return mixed|string
     */
    function getMassactionForCurrentUser()
    {
        $massaction = $this->getMassaction();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();

        return isset($massaction[$adminId]) ? $massaction[$adminId] : '{}';
    }

    /**
     * @return array|mixed
     */
    function getMassaction()
    {
        $massaction = Mage::getStoreConfig(IWD_OrderGrid_Helper_Data::CONFIG_XPATH_MASSACTION_SAVE);
        $massaction = unserialize($massaction);
        return empty($massaction) || !is_array($massaction) ? array() : $massaction;
    }

    /**
     * @param $massactionData
     */
    function saveMassactionForCurrentUser($massactionData)
    {
        $massaction = $this->getMassaction();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();
        $massaction[$adminId] = $massactionData;
        $massaction = serialize($massaction);

        Mage::getModel('core/config')->saveConfig(IWD_OrderGrid_Helper_Data::CONFIG_XPATH_MASSACTION_SAVE, $massaction);
    }
}
