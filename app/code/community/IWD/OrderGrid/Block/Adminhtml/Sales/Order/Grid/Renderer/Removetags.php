<?php

class IWD_OrderGrid_Block_Adminhtml_Sales_Order_Grid_Renderer_Removetags extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        return strip_tags(parent::render($row));
    }
}
