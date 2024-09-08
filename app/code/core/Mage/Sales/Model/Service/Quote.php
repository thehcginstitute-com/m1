<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Quote submit service model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Service_Quote
{
    /**
     * Quote object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote;

    /**
     * Quote convert object
     *
     * @var Mage_Sales_Model_Convert_Quote
     */
    protected $_convertor;

    /**
     * List of additional order attributes which will be added to order before save
     *
     * @var array
     */
    protected $_orderData = [];

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401

    /**
     * Order that may be created during submission
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order = null;

    /**
	 * 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Delete the unused «Nominal products» feature": https://github.com/thehcginstitute-com/m1/issues/407
	 *
     * If it is true, quote will be inactivate after submitting order
     *
     * @var bool
     */
    protected $_shouldInactivateQuote = true;

    /**
     * Class constructor
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    function __construct(Mage_Sales_Model_Quote $quote)
    {
        $this->_quote       = $quote;
        $this->_convertor   = Mage::getModel('sales/convert_quote');
    }

    /**
     * Quote convertor declaration
     *
     * @param   Mage_Sales_Model_Convert_Quote $convertor
     * @return  Mage_Sales_Model_Service_Quote
     */
    function setConvertor(Mage_Sales_Model_Convert_Quote $convertor)
    {
        $this->_convertor = $convertor;
        return $this;
    }

    /**
     * Get assigned quote object
     *
     * @return Mage_Sales_Model_Quote
     */
    function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Specify additional order data
     *
     * @param array $data
     * @return $this
     */
    function setOrderData(array $data)
    {
        $this->_orderData = $data;
        return $this;
    }

    /**
     * @deprecated after 1.4.0.1
     * @see submitOrder()
     * @see submitAll()
     */
    function submit()
    {
        return $this->submitOrder();
    }

    /**
     * Submit the quote. Quote submit process will create the order based on quote data
     *
     * @return Mage_Sales_Model_Order
     */
    function submitOrder()
    {
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Nominal products» feature": https://github.com/thehcginstitute-com/m1/issues/407
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();

        $transaction = Mage::getModel('core/resource_transaction');
        if ($quote->getCustomerId()) {
            $transaction->addObject($quote->getCustomer());
        }
        $transaction->addObject($quote);

        $quote->reserveOrderId();
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress());
        }
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
        if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddress($quote->getBillingAddress()->getCustomerAddress());
        }
        if (!$isVirtual) {
            $order->setShippingAddress($this->_convertor->addressToOrderAddress($quote->getShippingAddress()));
            if ($quote->getShippingAddress()->getCustomerAddress()) {
                $order->getShippingAddress()->setCustomerAddress($quote->getShippingAddress()->getCustomerAddress());
            }
        }
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        foreach ($this->_orderData as $key => $value) {
            $order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        $order->setQuote($quote);

        $transaction->addObject($order);
        $transaction->addCommitCallback([$order, 'place']);
        $transaction->addCommitCallback([$order, 'save']);

        Mage::unregister('current_order');
        Mage::register('current_order', $order);

        /**
         * We can use configuration data for declare new order status
         */
        Mage::dispatchEvent('checkout_type_onepage_save_order', ['order' => $order, 'quote' => $quote]);
        Mage::dispatchEvent('sales_model_service_quote_submit_before', ['order' => $order, 'quote' => $quote]);
        try {
            $transaction->save();
        } catch (Exception $e) {
            if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
                // reset customer ID's on exception, because customer not saved
                $quote->getCustomer()->setId(null);
            }

            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var Mage_Sales_Model_Order_Item $item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            Mage::dispatchEvent('sales_model_service_quote_submit_failure', ['order' => $order, 'quote' => $quote]);
            throw $e;
        }
        $this->_inactivateQuote();
        Mage::dispatchEvent('sales_model_service_quote_submit_success', ['order' => $order, 'quote' => $quote]);
        Mage::dispatchEvent('sales_model_service_quote_submit_after', ['order' => $order, 'quote' => $quote]);
        $this->_order = $order;
        return $order;
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# 1) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
	# 2) "Delete the unused «Nominal products» feature": https://github.com/thehcginstitute-com/m1/issues/407

    /**
     * Submit all available items
     * All created items will be set to the object
     */
    function submitAll()
    {
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Nominal products» feature": https://github.com/thehcginstitute-com/m1/issues/407
        // no need to submit the order if there are no normal items remained
        if (!$this->_quote->getAllVisibleItems()) {
            $this->_inactivateQuote();
            return;
        }
        $this->submitOrder();
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401

    /**
     * Get an order that may had been created during submission
     *
     * @return Mage_Sales_Model_Order
     */
    function getOrder()
    {
        return $this->_order;
    }

    /**
     * Inactivate quote
     *
     * @return $this
     */
    protected function _inactivateQuote()
    {
        if ($this->_shouldInactivateQuote) {
            $this->_quote->setIsActive(false);
        }
        return $this;
    }

    /**
     * Validate quote data before converting to order
     *
     * @return $this
     */
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                Mage::throwException(
                    Mage::helper('sales')->__('Please check shipping address information. %s', implode(' ', $addressValidation))
                );
            }
            $method = $address->getShippingMethod();
            $rate  = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                Mage::throwException(Mage::helper('sales')->__('Please specify a shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException(
                Mage::helper('sales')->__('Please check billing address information. %s', implode(' ', $addressValidation))
            );
        }

        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException(Mage::helper('sales')->__('Please select a valid payment method.'));
        }

        return $this;
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# 1) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
	# 2) "Delete the unused «Nominal products» feature": https://github.com/thehcginstitute-com/m1/issues/407
}
