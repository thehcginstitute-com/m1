<?php

/**
 * Class IWD_OrderFlags_Block_Adminhtml_Flags_Flags_Renderer_Icon
 */
class IWD_OrderFlags_Block_Adminhtml_Flags_Flags_Renderer_Icon
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
    /**
     * {@inheritdoc}
     */
    function render(Varien_Object $row)
    {
        /**
         * @var $row IWD_OrderFlags_Model_Flags_Flags
         */
        return $row->getIconHtml();
    }
}