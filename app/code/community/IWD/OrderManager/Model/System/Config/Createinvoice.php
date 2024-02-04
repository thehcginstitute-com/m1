<?php

/**
 * Class IWD_OrderManager_Model_System_Config_Createinvoice
 */
class IWD_OrderManager_Model_System_Config_Createinvoice
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'never',
                'label' => Mage::helper('adminhtml')->__('Never')
            ),
            array(
                'value' => 'always',
                'label' => Mage::helper('adminhtml')->__('Always')
            )
			# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) "Delete the unused `Mage_Paygate` module": https://github.com/thehcginstitute-com/m1/issues/354
			# 2) "Delete the unused `Mage_Authorizenet` module":
			# https://github.com/thehcginstitute-com/m1/issues/352
        );
    }
}
