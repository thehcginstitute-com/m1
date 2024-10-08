<?php

/**
 * Class IWD_AdminCheckout_Adminhtml_Sales_CreateController
 */
class IWD_AdminCheckout_Adminhtml_Sales_CreateController extends IWD_OrderManager_Controller_Abstract
{
    /**
     * @return void
     */
    function updateStoreAction()
    {
        $storeId = $this->getRequest()->getParam('store_id', 1);
        $quote = Mage::getSingleton('adminhtml/session_quote')->getQuote();
        $quote->setStoreId($storeId)->save();

        $this->prepareResponse(array('status' => 1));
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
