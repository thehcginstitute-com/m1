<?php

/**
 * Class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Data
 */
class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Data extends Mage_Adminhtml_Block_Sales_Order_Create_Data
{
    /**
     * @return string
     */
    function _toHtml()
    {
        if (Mage::helper('iwd_admin_checkout')->isCustomCreationProcess()) {
            $this->setTemplate('iwd/admincheckout/create/data.phtml');
        }

        return parent::_toHtml();
    }
}
