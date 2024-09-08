<?php

/**
 * Class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Top
 */
class IWD_AdminCheckout_Block_Adminhtml_Sales_Order_Create_Top extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    /**
     * @return array
     */
    function getAvailableCurrencies()
    {
        $dirtyCodes = $this->getStore()->getAvailableCurrencyCodes();
        $codes = array();
        if (is_array($dirtyCodes) && count($dirtyCodes)) {
            $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                Mage::app()->getStore()->getBaseCurrency(),
                $dirtyCodes
            );
            foreach ($dirtyCodes as $code) {
                if (isset($rates[$code]) || $code == Mage::app()->getStore()->getBaseCurrencyCode()) {
                    $codes[] = $code;
                }
            }
        }

        return $codes;
    }

    /**
     * @param $code
     * @return string
     */
    function getCurrencyName($code)
    {
        return Mage::app()->getLocale()->currency($code)->getName();
    }

    /**
     * @param $code
     * @return string
     */
    function getCurrencySymbol($code)
    {
        $currency = Mage::app()->getLocale()->currency($code);
        return $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
    }

    /**
     * @return string
     */
    function getCurrentCurrencyCode()
    {
        return $this->getStore()->getCurrentCurrencyCode();
    }
}
