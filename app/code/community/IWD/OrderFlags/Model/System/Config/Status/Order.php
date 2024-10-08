<?php

/**
 * Class IWD_OrderFlags_Model_System_Config_Status_Order
 */
class IWD_OrderFlags_Model_System_Config_Status_Order
{
    /**
     * @return array
     */
    function toOptionArray()
    {
        $statuses = Mage::getSingleton('sales/order_config')->getStatuses();
        
        $options = array(
            array(
               'value' => false,
               'label' => Mage::helper('adminhtml')->__('-- Not select --')
            )
        );
            
        foreach ($statuses as $code=>$label) {
            $options[] = array(
               'value' => $code,
               'label' => $label
            );
        }
        
        return $options;
    }
}
