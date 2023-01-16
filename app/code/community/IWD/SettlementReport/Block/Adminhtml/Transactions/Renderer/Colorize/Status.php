<?php

/**
 * Class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Colorize_Status
 */
class IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Colorize_Status
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    /**
     * @var bool|mixed
     */
    protected $export = false;

    /**
     * IWD_SettlementReport_Block_Adminhtml_Transactions_Renderer_Colorize_Status constructor.
     * @param array $args
     */
    public function __construct(array $args = array())
    {
        if (isset($args['export'])) {
            $this->export = $args['export'];
        }

        parent::__construct($args);
    }

    public function render(Varien_Object $row)
    {
        if ($this->export) {
            return parent::render($row);
        }

        $currentStatus = $row->getData('mage_transaction_status');
        $compareStatus = $row->getData('auth_transaction_status');

        $equal = false;
        switch ($currentStatus) {
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT:
                $equal = false; /* I don't know when this status uses */
                break;
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER:
                $equal = ($compareStatus == "authorizedPendingCapture"); /* Pending approval on gateway */
                break;
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH:
                $equal = ($compareStatus == "authorizedPendingCapture");
                break;
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE:
                $equal = ($compareStatus == "capturedPendingSettlement" || $compareStatus == "settledSuccessfully");
                break;
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID:
                $equal = ($compareStatus == "voided");
                break;
            case Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND:
                $equal = ($compareStatus == "refundSettledSuccessfully" || $compareStatus == "refundPendingSettlement");
                break;
        }

        $status = (string)parent::render($row);

        return $equal ? $status : "<b style='color:#C54E35;'>{$status}</b>";
    }
}
