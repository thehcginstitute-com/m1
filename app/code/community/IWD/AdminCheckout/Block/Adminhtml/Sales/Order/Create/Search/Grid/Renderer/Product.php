<?php

/**
 * Class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Search_Grid_Renderer_Product
 */
class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Search_Grid_Renderer_Product
    extends Mage_Adminhtml_Block_Sales_Order_Create_Search_Grid_Renderer_Product
{
    /**
     * @param   Varien_Object $row
     * @return  string
     */
    function render(Varien_Object $row)
    {
        $product = Mage::getModel('catalog/product')->load($row->getId());
        if (!$product->isVisibleInSiteVisibility()) {
            return parent::render($row);
        } else {
            return parent::render($row)
                . "<span class='f-right'>&nbsp;&nbsp;|&nbsp;&nbsp;</span>"
                . sprintf(
                    '<a href="%s" class="f-right" target="_blank">%s</a>',
                    $product->getProductUrl(),
                    Mage::helper('iwd_admin_checkout')->__('Preview')
                );
        }
    }
}
