<?php

class IWD_OrderManager_Block_Adminhtml_Sales_Order_Shipping_Form extends Mage_Adminhtml_Block_Widget
{
    protected $shippingRates;

    function __construct()
    {
        $this->shippingRates = null;
        parent::__construct();
        $this->setTemplate('iwd/ordermanager/shipping/form.phtml');
    }

    function getCustomMethodCode()
    {
        return IWD_OrderManager_Model_Shipping::CUSTOM_METHOD_CODE;
    }

    function displayPriceAttribute($code, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/sales')->displayPriceAttribute($this->getPriceDataObject(), $code, $strong, $separator);
    }

    function displayPrices($basePrice, $price, $strong = false, $separator = '<br/>')
    {
        return $this->helper('adminhtml/sales')->displayPrices($this->getPriceDataObject(), $basePrice, $price, $strong, $separator);
    }

    function displayShippingPriceInclTax($order)
    {
        $shipping = $order->getShippingInclTax();
        if ($shipping) {
            $baseShipping = $order->getBaseShippingInclTax();
        } else {
            $shipping = $order->getShippingAmount() + $order->getShippingTaxAmount();
            $baseShipping = $order->getBaseShippingAmount() + $order->getBaseShippingTaxAmount();
        }

        return $this->displayPrices($baseShipping, $shipping, false, ' ');
    }

    function getAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    function getShippingRates()
    {
        $order = $this->getOrder();
        $this->shippingRates = Mage::getModel("iwd_ordermanager/shipping")->getShippingRates($order);
        return $this->shippingRates;
    }

    function getActiveMethodRate()
    {
        if ($this->getCustomMethodCode() == $this->getOrder()->getShippingMethod()) {
            $rate = new Varien_Object();
            $rate->setCode($this->getCustomMethodCode());
            $rate->setPrice($this->getOrder()->getShippingAmount());
            $rate->setMethodTitle(Mage::helper('iwd_ordermanager')->__("Custom"));
            $rate->setMethodDescription($this->getOrder()->getShippingDescription());
            return $rate;
        }

        if (is_array($this->shippingRates)) {
            foreach ($this->shippingRates as $group) {
                foreach ($group as $rate) {
                    if ($rate->getCode() == $this->getOrder()->getShippingMethod()) {
                        return $rate;
                    }
                }
            }
        }

        return false;
    }

    function isMethodActive($code)
    {
        return $code === $this->getOrder()->getShippingMethod();
    }

    function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/' . $carrierCode . '/title', $this->getOrder()->getStoreId())) {
            return $name;
        }

        return $carrierCode;
    }

    function getShippingPrice($price, $flag)
    {
        $store = Mage::getModel('core/store')->load($this->getOrder()->getStoreId());
        return Mage::helper('tax')->getShippingPrice($price, $flag, $this->getAddress(), null, $store);
    }

    function formatPrice($price)
    {
        return Mage::getModel('core/store')
            ->load($this->getOrder()->getStoreId())
            ->convertPrice($price, true);
    }

    /**
     * @param $exclPrice
     * @param $inclPrice
     * @return float|int
     */
    function getShippingPercent($exclPrice, $inclPrice)
    {
        return Mage::helper('iwd_ordermanager')->getRoundPercent($exclPrice, $inclPrice);
    }
}
