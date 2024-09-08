<?php

class IWD_OrderGrid_Model_System_Config_Gridcolumn_Order
{
    /**
     * @return array
     */
    protected function getSelectedColumnsArray()
    {
        return Mage::getModel('iwd_ordergrid/order_grid')
            ->getSelectedColumnsArray(IWD_OrderGrid_Model_Order_Grid::XML_PATH_ORDER_GRID_COLUMN);
    }

    protected function allowDispatchEvent()
    {
        return true;
    }

    /**
     * @return array
     */
    function toOptionArray()
    {
        $selected = $this->getSelectedColumnsArray();
        $columns = Mage::getModel('iwd_ordergrid/order_grid')->getOrderGridColumns(
            $this->allowDispatchEvent()
        );

        $options = array();
        foreach ($selected as $sel) {
            if (isset($columns[$sel])) {
                $options[] = array(
                    'value' => $sel,
                    'label' => $columns[$sel]
                );
                unset($columns[$sel]);
            }
        }

        foreach ($columns as $code => $label) {
            $options[] = array(
                'value' => $code,
                'label' => $label
            );
        }

        return $options;
    }
}