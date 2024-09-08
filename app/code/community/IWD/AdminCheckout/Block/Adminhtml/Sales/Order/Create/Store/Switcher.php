<?php

/**
 * Class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Store_Switcher
 */
class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Store_Switcher extends Mage_Adminhtml_Block_Store_Switcher
{
    /**
     * @return int
     */
    function getStoreId()
    {
        $store = $this->getSessionStoreId();
        if (empty($store)) {
            $store = $this->getSessionStoreId();
        }

        return $this->getRequest()->getParam($this->_storeVarName, $store);
    }

    /**
     * @return int
     */
    function getDefaultStoreId()
    {
        return Mage::helper('iwd_admin_checkout')->getDefaultStoreView();
    }

    /**
     * @return int
     */
    function getSessionStoreId()
    {
        return $this->_getSession()->getStoreId();
    }

    /**
     * @return Mage_Adminhtml_Model_Session_Quote
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session_quote');
    }
}
