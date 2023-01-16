<?php

/**
 * Class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Form
 */
class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Form
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        if (Mage::helper('iwd_admin_checkout')->isCustomCreationProcess()) {
            $this->insert('top_actions', 'form');
            $this->setTemplate('iwd/admincheckout/create/form.phtml');
        }

        return parent::_toHtml();
    }

    /**
     * @return int
     */
    public function getDefaultStoreView()
    {
        return Mage::helper('iwd_admin_checkout')->getDefaultStoreView();
    }
}
