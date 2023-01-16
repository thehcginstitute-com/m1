<?php

/**
 * Class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Order
 */
class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Order
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var bool|mixed
     */
    protected $export = false;

    /**
     * IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Order constructor.
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        if (isset($args['export'])) {
            $this->export = $args['export'];
        }

        parent::__construct($args);
    }

    /**
     * @param Varien_Object $row
     * @return mixed|string
     */
    public function render(Varien_Object $row)
    {
        if ($this->export) {
            return $row['order_increment_id'];
        }

        $url = Mage::helper('adminhtml')->getUrl('adminhtml/sales_order/view', array('order_id' => $row['order_id']));
        return "<a href='{$url}' title='{$row['order_id']}' target='_blank'>{$row['order_increment_id']}</a>";
    }
}
