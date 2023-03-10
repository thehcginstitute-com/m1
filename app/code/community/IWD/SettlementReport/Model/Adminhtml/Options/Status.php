<?php

/**
 * Class IWD_SettlementReport_Model_Adminhtml_Options_Status
 */
class IWD_SettlementReport_Model_Adminhtml_Options_Status
{
    /**
     * @return array
     */
    public function toOptionArrayAuth()
    {
        $helper = Mage::helper("iwd_settlementreport");

        return array(
            "authorizedPendingCapture" => $helper->__("Authorized"),
            "capturedPendingSettlement" => $helper->__("Captured"),
            "settledSuccessfully" => $helper->__("Settled"),
            "refundPendingSettlement" => $helper->__("Refund Pending"),
            "refundSettledSuccessfully" => $helper->__("Refund Settled"),
            "voided" => $helper->__("Voided"),
            "expired" => $helper->__("Expired"),
            "declined" => $helper->__("Declined"),
        );
    }

    /**
     * @return array
     */
    public function toOptionArrayMage()
    {
        $helper = Mage::helper("iwd_settlementreport");

        return array(
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_ORDER => $helper->__('Ordered'),
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH => $helper->__('Authorized'),
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE => $helper->__('Captured'),
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID => $helper->__('Voided'),
            Mage_Sales_Model_Order_Payment_Transaction::TYPE_REFUND => $helper->__('Refunded')
        );
    }
}