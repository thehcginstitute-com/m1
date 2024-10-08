<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2017-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Checkout default helper
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_PATH_GUEST_CHECKOUT = 'checkout/options/guest_checkout';
    public const XML_PATH_CUSTOMER_MUST_BE_LOGGED = 'checkout/options/customer_must_be_logged';

    protected $_moduleName = 'Mage_Checkout';

    protected $_agreements = null;

    /**
     * Retrieve checkout session model
     *
     * @return Mage_Checkout_Model_Session
     */
    function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Retrieve checkout quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * @param float $price
     * @return string
     */
    function formatPrice($price)
    {
        return $this->getQuote()->getStore()->formatPrice($price);
    }

    /**
     * @param float $price
     * @param bool $format
     * @return float
     */
    function convertPrice($price, $format = true)
    {
        return $this->getQuote()->getStore()->convertPrice($price, $format);
    }

    /**
     * @return array|null
     * @throws Mage_Core_Model_Store_Exception
     */
    function getRequiredAgreementIds()
    {
        if (is_null($this->_agreements)) {
            if (!Mage::getStoreConfigFlag('checkout/options/enable_agreements')) {
                $this->_agreements = [];
            } else {
                $this->_agreements = Mage::getModel('checkout/agreement')->getCollection()
                    ->addStoreFilter(Mage::app()->getStore()->getId())
                    ->addFieldToFilter('is_active', 1)
                    ->getAllIds();
            }
        }
        return $this->_agreements;
    }

    /**
     * Get onepage checkout availability
     *
     * @return bool
     */
    function canOnepageCheckout()
    {
        return Mage::getStoreConfigFlag('checkout/options/onepage_checkout_enabled');
    }

    /**
     * Get sales item (quote item, order item etc) price including tax based on row total and tax amount
     * excluding weee tax
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  float
     */
    function getPriceInclTax($item)
    {
        if ($item->getPriceInclTax()) {
            return $item->getPriceInclTax();
        }
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));

        //Unit price is rowtotal/qty
        return $qty > 0 ? $this->getSubtotalInclTax($item) / $qty : 0;
    }

    /**
     * Get sales item (quote item, order item etc) row total price including tax
     *
     * @param   Mage_Core_Model_Abstract $item
     * @return  float
     */
    function getSubtotalInclTax($item)
    {
        if ($item->getRowTotalInclTax()) {
            return $item->getRowTotalInclTax();
        }
		# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused `Mage_Weee` module": https://github.com/thehcginstitute-com/m1/issues/377
        $tax = $item->getTaxAmount() + $item->getDiscountTaxCompensation();

        return $item->getRowTotal() + $tax;
    }

	# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused `Mage_Weee` module": https://github.com/thehcginstitute-com/m1/issues/377

    /**
     * Get the base price of the item including tax , excluding weee
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    function getBasePriceInclTax($item)
    {
        $qty = ($item->getQty() ? $item->getQty() : ($item->getQtyOrdered() ? $item->getQtyOrdered() : 1));

        return $qty > 0 ? $this->getBaseSubtotalInclTax($item) / $qty : 0;
    }

    /**
     * Get sales item (quote item, order item etc) row total price including tax excluding wee
     *
     * @param Mage_Core_Model_Abstract $item
     * @return float
     */
    function getBaseSubtotalInclTax($item)
    {
		# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused `Mage_Weee` module": https://github.com/thehcginstitute-com/m1/issues/377
        $tax = $item->getBaseTaxAmount() + $item->getBaseDiscountTaxCompensation();
        return $item->getBaseRowTotal() + $tax;
    }

    /**
     * Send email id payment was failed
     *
     * @param Mage_Sales_Model_Quote $checkout
     * @param string $message
     * @param string $checkoutType
     * @return $this
     */
    function sendPaymentFailedEmail($checkout, $message, $checkoutType = 'onepage')
    {
        $translate = Mage::getSingleton('core/translate');
        /** @var Mage_Core_Model_Translate $translate */
        $translate->setTranslateInline(false);

        $mailTemplate = Mage::getModel('core/email_template');
        /** @var Mage_Core_Model_Email_Template $mailTemplate */

        $template = Mage::getStoreConfig('checkout/payment_failed/template', $checkout->getStoreId());

        $copyTo = $this->_getEmails('checkout/payment_failed/copy_to', $checkout->getStoreId());
        $copyMethod = Mage::getStoreConfig('checkout/payment_failed/copy_method', $checkout->getStoreId());
        if ($copyTo && $copyMethod == 'bcc') {
            $mailTemplate->addBcc($copyTo);
        }

        $_reciever = Mage::getStoreConfig('checkout/payment_failed/reciever', $checkout->getStoreId());
        $sendTo = [
            [
                'email' => Mage::getStoreConfig('trans_email/ident_' . $_reciever . '/email', $checkout->getStoreId()),
                'name'  => Mage::getStoreConfig('trans_email/ident_' . $_reciever . '/name', $checkout->getStoreId())
            ]
        ];

        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $sendTo[] = [
                    'email' => $email,
                    'name'  => null
                ];
            }
        }
        $shippingMethod = '';
        if ($shippingInfo = $checkout->getShippingAddress()->getShippingMethod()) {
            $data = explode('_', $shippingInfo);
            $shippingMethod = $data[0];
        }

        $paymentMethod = '';
        if ($paymentInfo = $checkout->getPayment()) {
            $paymentMethod = $paymentInfo->getMethod();
        }

        $items = '';
        foreach ($checkout->getAllVisibleItems() as $_item) {
            /** @var Mage_Sales_Model_Quote_Item $_item */
            $items .= $_item->getProduct()->getName() . '  x ' . $_item->getQty() . '  '
                . $checkout->getStoreCurrencyCode() . ' '
                . $_item->getProduct()->getFinalPrice($_item->getQty()) . "\n";
        }
        $total = $checkout->getStoreCurrencyCode() . ' ' . $checkout->getGrandTotal();

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(['area' => 'frontend', 'store' => $checkout->getStoreId()])
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig('checkout/payment_failed/identity', $checkout->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    [
                        'reason'          => $message,
                        'checkoutType'    => $checkoutType,
                        'dateAndTime'     => Mage::app()->getLocale()->date(),
                        'customer'        => Mage::helper('customer')->getFullCustomerName($checkout),
                        'customerEmail'   => $checkout->getCustomerEmail(),
                        'billingAddress'  => $checkout->getBillingAddress(),
                        'shippingAddress' => $checkout->getShippingAddress(),
                        'shippingMethod'  => Mage::getStoreConfig('carriers/' . $shippingMethod . '/title'),
                        'paymentMethod'   => Mage::getStoreConfig('payment/' . $paymentMethod . '/title'),
                        'items'           => nl2br($items),
                        'total'           => $total,
                    ]
                );
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * @param string $configPath
     * @param int $storeId
     * @return array|false
     */
    protected function _getEmails($configPath, $storeId)
    {
        $data = Mage::getStoreConfig($configPath, $storeId);
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    /**
     * Check if multishipping checkout is available.
     * There should be a valid quote in checkout session. If not, only the config value will be returned.
     *
     * @return bool
     */
    function isMultishippingCheckoutAvailable()
    {
        $quote = $this->getQuote();
        $isMultiShipping = (bool)(int)Mage::getStoreConfig('shipping/option/checkout_multiple');
        if ((!$quote) || !$quote->hasItems()) {
            return $isMultiShipping;
        }
        $maximunQty = (int)Mage::getStoreConfig('shipping/option/checkout_multiple_maximum_qty');
        return $isMultiShipping
            && !$quote->hasItemsWithDecimalQty()
            && $quote->validateMinimumAmount(true)
            && (($quote->getItemsSummaryQty() - $quote->getItemVirtualQty()) > 0)
            && ($quote->getItemsSummaryQty() <= $maximunQty)
			# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Delete the unused «Nominal products» feature": https://github.com/thehcginstitute-com/m1/issues/407
        ;
    }

    /**
     * Check is allowed Guest Checkout
     * Use config settings and observer
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param int|Mage_Core_Model_Store $store
     * @return bool
     */
    function isAllowedGuestCheckout(Mage_Sales_Model_Quote $quote, $store = null)
    {
        if ($store === null) {
            $store = $quote->getStoreId();
        }
        $guestCheckout = Mage::getStoreConfigFlag(self::XML_PATH_GUEST_CHECKOUT, $store);

        if ($guestCheckout == true) {
            $result = new Varien_Object();
            $result->setIsAllowed($guestCheckout);
            Mage::dispatchEvent('checkout_allow_guest', [
                'quote'  => $quote,
                'store'  => $store,
                'result' => $result
            ]);

            $guestCheckout = $result->getIsAllowed();
        }

        return $guestCheckout;
    }

    /**
     * Check if context is checkout
     *
     * @return bool
     */
    function isContextCheckout()
    {
        return (Mage::app()->getRequest()->getParam('context') == 'checkout');
    }

    /**
     * Check if user must be logged during checkout process
     *
     * @return bool
     */
    function isCustomerMustBeLogged()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_CUSTOMER_MUST_BE_LOGGED);
    }
}
