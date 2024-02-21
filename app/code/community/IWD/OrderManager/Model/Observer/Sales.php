<?php

/**
 * Class IWD_OrderManager_Model_Observer_Sales
 */
class IWD_OrderManager_Model_Observer_Sales
{
	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused «Backup Sales» feature of `IWD_OrderManager`": https://github.com/thehcginstitute-com/m1/issues/412

    /**
     * @param Varien_Event_Observer $observer
     * @return $this
     */
    public function addFeeToQuote(Varien_Event_Observer $observer)
    {
        $request = $observer->getEvent()->getRequest();

        $quote = $observer->getEvent()->getOrderCreateModel()->getQuote();

        if (isset($request['iwd_om_fee_amount'])) {
            $feeAmount = $request['iwd_om_fee_amount'];
            $baseFeeAmount = $this->convertToBaseAmount($feeAmount, $quote);
            $quote->setIwdOmFeeAmount($feeAmount)->setIwdOmFeeBaseAmount($baseFeeAmount);
        }

        if (isset($request['iwd_om_fee_amount_incl_tax'])) {
            $feeAmountInclTax = $request['iwd_om_fee_amount_incl_tax'];
            $baseFeeAmountInclTax = $this->convertToBaseAmount($feeAmountInclTax, $quote);
            $quote->setIwdOmFeeAmountInclTax($feeAmountInclTax)->setIwdOmFeeBaseAmountInclTax($baseFeeAmountInclTax);
        }

        if (isset($request['iwd_om_fee_description'])) {
            $description = $request['iwd_om_fee_description'];
            $quote->setIwdOmFeeDescription($description);
        }

        if (isset($request['iwd_om_fee_tax_percent'])) {
            $taxPercent = $request['iwd_om_fee_tax_percent'];
            $quote->setIwdOmFeeTaxPercent($taxPercent);
        }

        try {
            $quote->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('adminhtml/session_quote')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session_quote')->addException(
                $e, Mage::helper('opc')->__('Cannot apply custom amount')
            );
        }

        return $this;
    }

    /**
     * @param $feeAmount
     * @param $quote
     * @return float
     */
    protected function convertToBaseAmount($feeAmount, $quote)
    {
        $baseCurrencyCode = $quote->getStore()->getBaseCurrencyCode();
        $currentCurrencyCode = $quote->getStore()->getCurrentCurrencyCode();

        $baseFeeAmount = ($baseCurrencyCode != $currentCurrencyCode)
            ? Mage::helper('directory')->currencyConvert($feeAmount, $currentCurrencyCode, $baseCurrencyCode)
            : $feeAmount;

        return round($baseFeeAmount, 2);
    }

    /**
     * @param Varien_Event_Observer $observer
     */
    public function changeOrderStatus(Varien_Event_Observer $observer)
    {
        $status = Mage::getStoreConfig('iwd_ordermanager/inventory/order_outofstock_status');
        if (!$status) {
            return;
        }

        $outofstockAllItems = Mage::getStoreConfig('iwd_ordermanager/inventory/order_outofstock_all_items');

        /** @var $order Mage_Sales_Model_Order */
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllItems();
        $hasBackorderedItems = false;
        foreach ($items as $item) {
            /** @var $item Mage_Sales_Model_Order_Item */
            if ($item->getQtyBackordered() > 0) {
                $hasBackorderedItems = true;
            } elseif ($outofstockAllItems) {
                $hasBackorderedItems = false;
                break;
            }
        }

        if ($hasBackorderedItems) {
            $order->setStatus($status);
        }
    }
}
