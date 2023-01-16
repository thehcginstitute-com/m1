<?php

class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Longstring extends IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Abstract
{
    protected function Export()
    {
        $this->_getValue($this->row);
    }

    protected function Grid()
    {
        return $this->wrapper($this->_getValue($this->row));
    }

    protected function wrapper($val)
    {
        $id = $this->getOrderId();
        return sprintf('<div style="position:relative"><span class="iwd_long_string_in_grid hide">%s</span><a class="iwd_order_grid_more show row-%s" href="javascript:void(0);" data-row-id="%s" title="%s"></a></div>',
            $val, $id, $id, Mage::helper('iwd_ordergrid')->__('Show/hide'));
    }
}