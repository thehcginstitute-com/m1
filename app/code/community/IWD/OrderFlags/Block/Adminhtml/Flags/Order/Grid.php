<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Order_Grid
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Order_Grid extends Mage_Adminhtml_Block_Template
{
    /**
     * @return mixed|string|void
     */
    function getFlagsForTypes()
    {
        $collection = Mage::getModel('iwd_orderflags/flags_flag_type')->getCollection()
            ->addFieldToSelect(
                array(
                    'type_id' => 'type_id',
                    'flags' => new Zend_Db_Expr("group_concat(DISTINCT main_table.flag_id SEPARATOR \", \")")
                )
            );
        $collection->getSelect()->group('main_table.type_id');

        $flags = array();
        foreach ($collection as $item) {
            $flags[$item->getTypeId()] = explode(',', $item->getFlags());
        }

        return json_encode($flags);
    }

    /**
     * @return bool
     */
    function isEnabled()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_orderflags/assign_flags');
    }
}
