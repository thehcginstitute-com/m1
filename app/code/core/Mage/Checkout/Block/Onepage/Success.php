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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout success page
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setCanViewProfiles(bool $value)
 * 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
 */
class Mage_Checkout_Block_Onepage_Success extends Mage_Core_Block_Template
{
    /**
     * @deprecated after 1.4.0.1
     */
    private $_order;

    /**
     * Retrieve identifier of created order
     *
     * @return string
     * @deprecated after 1.4.0.1
     */
    function getOrderId()
    {
        return $this->_getData('order_id');
    }

    /**
     * Check order print availability
     *
     * @return bool
     * @deprecated after 1.4.0.1
     */
    function canPrint()
    {
        return $this->_getData('can_view_order');
    }

    /**
     * Get url for order detale print
     *
     * @return string
     * @deprecated after 1.4.0.1
     */
    function getPrintUrl()
    {
        return $this->_getData('print_url');
    }

    /**
     * Get url for view order details
     *
     * @return string
     * @deprecated after 1.4.0.1
     */
    function getViewOrderUrl()
    {
        return $this->_getData('view_order_id');
    }

    /**
     * See if the order has state, visible on frontend
     *
     * @return bool
     */
    function isOrderVisible()
    {
        return (bool)$this->_getData('is_order_visible');
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401

    /**
     * Initialize data and prepare it for output
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastOrder();
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# 1) "Delete the unused «Billing Agreements» feature": https://github.com/thehcginstitute-com/m1/issues/400
		# 2) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
        return parent::_beforeToHtml();
    }

    /**
     * Get last order ID from session, fetch it and check whether it can be viewed, printed etc
     */
    protected function _prepareLastOrder()
    {
        $orderId = Mage::getSingleton('checkout/session')->getLastOrderId();
        if ($orderId) {
            $order = Mage::getModel('sales/order')->load($orderId);
            if ($order->getId()) {
                $isVisible = !in_array(
                    $order->getState(),
                    Mage::getSingleton('sales/order_config')->getInvisibleOnFrontStates()
                );
                $this->addData([
                    'is_order_visible' => $isVisible,
                    'view_order_id' => $this->getUrl('sales/order/view/', ['order_id' => $orderId]),
                    'print_url' => $this->getUrl('sales/order/print', ['order_id' => $orderId]),
                    'can_print_order' => $isVisible,
                    'can_view_order'  => Mage::getSingleton('customer/session')->isLoggedIn() && $isVisible,
                    'order_id'  => $order->getIncrementId(),
                    'order' => $order,
                ]);
            }
        }
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# 1) "Delete the unused «Billing Agreements» feature": https://github.com/thehcginstitute-com/m1/issues/400
	# 2) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
}
