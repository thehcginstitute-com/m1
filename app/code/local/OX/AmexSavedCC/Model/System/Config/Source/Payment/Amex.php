<?php
class OX_AmexSavedCC_Model_System_Config_Source_Payment_Amex
{
    public function toOptionArray()
    {
        $options =  array();
        $options[] = array(
            'value' => 'AE',
            'label' => Mage::helper('amexsavedcc')->__('American Express')
        );
        return $options;
    }
}