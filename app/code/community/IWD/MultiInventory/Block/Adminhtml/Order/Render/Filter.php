<?php

class IWD_MultiInventory_Block_Adminhtml_Order_Render_Filter
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    public function _getOptions()
    {
        return IWD_MultiInventory_Model_Cataloginventory_Stock_Order_Status::getStatusesOptionArray();
    }
}