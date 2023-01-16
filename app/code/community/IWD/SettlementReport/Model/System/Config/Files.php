<?php

/**
 * Class IWD_SettlementReport_Model_System_Config_Files
 */
class IWD_SettlementReport_Model_System_Config_Files
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $helper = Mage::helper('iwd_settlementreport');
        return array(
            array(
                'value' => 'csv',
                'label' => $helper->__('CSV file')
            ),
            array(
                'value' => 'xml',
                'label' => $helper->__('Excel XML file')
            )
        );
    }
}