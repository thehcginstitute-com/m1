<?php

/**
 * Class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Status
 */
class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * @var bool|mixed
     */
    protected $export = false;

    /**
     * IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Status constructor.
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
            return $row['status'];
        }

        if ((int)$row['status'] == 1) {
            $alt = Mage::helper('iwd_settlementreport')->__('Good');
            $img = $this->getSkinUrl('images/success_msg_icon.gif');
        } else {
            $alt = Mage::helper('iwd_settlementreport')->__('Bad');
            $img = $this->getSkinUrl('images/error_msg_icon.gif');
        }

        $trxId = $row['payment_transaction_id'];
        return "<img src='{$img}' alt='{$alt}' title='{$alt}' data-trx-id='{$trxId}'/>";
    }
}
