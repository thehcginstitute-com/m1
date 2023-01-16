<?php

/**
 * Class IWD_SettlementReport_Block_System_Config_Form_Fieldset_CurrentTime
 */
class IWD_SettlementReport_Block_System_Config_Form_Fieldset_CurrentTime
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * {@inheritdoc}
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return strftime('%Y-%m-%d %H:%M:00', Mage::getSingleton('core/date')->timestamp(now()));
    }
}
