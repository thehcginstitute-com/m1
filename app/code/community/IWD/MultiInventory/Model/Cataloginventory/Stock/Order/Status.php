<?php

/**
 * Class IWD_MultiInventory_Model_Cataloginventory_Stock_Order_Status
 */
class IWD_MultiInventory_Model_Cataloginventory_Stock_Order_Status
{
    /**
     * @return array
     */
    public static function getStatuses()
    {
        $helper = Mage::helper('iwd_multiinventory');

        return array(
            1 => $helper->__('Assigned'),
            0 => $helper->__('Not Assigned'),
            -1 => $helper->__('Not Applicable')
        );
    }

    /**
     * @return array
     */
    public static function getStatusesOptionArray()
    {
        $helper = Mage::helper('iwd_multiinventory');

        return array(
            array('value' => '', 'label' => ''),
            array('value' => 1, 'label' => $helper->__('Assigned')),
            array('value' => 0, 'label' => $helper->__('Not Assigned')),
            array('value' => -1, 'label' => $helper->__('Not Applicable')),
        );
    }
}