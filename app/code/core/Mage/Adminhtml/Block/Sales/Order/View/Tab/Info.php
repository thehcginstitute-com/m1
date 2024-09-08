<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order information tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Tab_Info extends Mage_Adminhtml_Block_Sales_Order_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    function getOrder()
    {
        return Mage::registry('current_order');
    }

    /**
     * Retrieve source model instance
     *
     * @return Mage_Sales_Model_Order
     */
    function getSource()
    {
        return $this->getOrder();
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    function getOrderTotalData()
    {
        return [
            'can_display_total_due'      => true,
            'can_display_total_paid'     => true,
            'can_display_total_refunded' => true,
        ];
    }

    function getOrderInfoData()
    {
        return [
            'no_use_order_link' => true,
        ];
    }

    function getTrackingHtml()
    {
        return $this->getChildHtml('order_tracking');
    }

    function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    /**
     * Retrieve giftmessage block html
     *
     * @deprecated after 1.4.2.0, use self::getGiftOptionsHtml() instead
     * @return string
     */
    function getGiftmessageHtml()
    {
        return $this->getChildHtml('order_giftmessage');
    }

    /**
     * Retrieve gift options container block html
     *
     * @return string
     */
    function getGiftOptionsHtml()
    {
        return $this->getChildHtml('gift_options');
    }

    function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    function getViewUrl($orderId)
    {
        return $this->getUrl('*/*/*', ['order_id' => $orderId]);
    }

    /**
     * ######################## TAB settings #################################
     */
    function getTabLabel()
    {
        return Mage::helper('sales')->__('Information');
    }

    function getTabTitle()
    {
        return Mage::helper('sales')->__('Order Information');
    }

    function canShowTab()
    {
        return true;
    }

    function isHidden()
    {
        return false;
    }
}
