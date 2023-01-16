<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Sales_Order_Grid_Renderer_Flags
 */
class IWD_OrderFlags_Block_Adminhtml_Sales_Order_Grid_Renderer_Flags extends IWD_OrderFlags_Block_Adminhtml_Sales_Order_Grid_Renderer_Abstract
{
    protected $flags = [];

    /**
     * {@inheritdoc}
     */
    protected function Export()
    {
        $flagId = $this->_getValue($this->row);

        try {
            $flag = $this->getFlag($flagId);
            $flagLabel = $flag->getName();
        } catch (\Exception $e) {
            $flagLabel = $flagId;
        }

        return $flagLabel;
    }

    /**
     * {@inheritdoc}
     */
    protected function Grid()
    {
        $flagType = str_replace('iwd_om_flags_', '', $this->getColumn()->getIndex());
        $flagId = $this->_getValue($this->row);
        $orderId = $this->row->getData('entity_id');
        $orderIncrementId = $this->row->getData('increment_id');

        $flag = $this->getFlag($flagId);
        $html = $flag->getIconHtmlWithHint();

        return sprintf(
            '<a class="iwd-om-flag-cell" href="javascript:void(0);" title="" data-order-id="%s" data-order-increment-id="%s" data-flag-type="%s">%s</a>',
            $orderId,
            $orderIncrementId,
            $flagType,
            $html
        );
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function getFlag($id)
    {
        if (!isset($this->flags[$id])) {
            $this->flags[$id] = Mage::getModel('iwd_orderflags/flags_flags')->load($id);
        }

        return $this->flags[$id];
    }
}
