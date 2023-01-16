<?php

class IWD_MultiInventory_Block_Adminhtml_Order_Creditmemo_Create_Items extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Create_Items
{
    protected function _toHtml()
    {
        if (Mage::helper('iwd_multiinventory')->isMultiInventoryEnable()) {
            $this->setTemplate('iwd/multiinventory/creditmemo/items.phtml');
        }

        return parent::_toHtml();
    }
}