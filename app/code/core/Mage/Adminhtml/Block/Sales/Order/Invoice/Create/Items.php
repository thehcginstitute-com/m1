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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml invoice items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    protected $_disableSubmitButton = false;

    /**
     * Prepare child blocks
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $onclick = "submitAndReloadArea($('invoice_item_container'),'" . $this->getUpdateUrl() . "')";
        $this->setChild(
            'update_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'class'     => 'update-button',
                'label'     => Mage::helper('sales')->__('Update Qty\'s'),
                'onclick'   => $onclick,
            ])
        );
        $this->_disableSubmitButton = true;
        $_submitButtonClass = ' disabled';
        foreach ($this->getInvoice()->getAllItems() as $item) {
            /**
             * @see bug #14839
             */
            if ($item->getQty()/* || $this->getSource()->getData('base_grand_total')*/) {
                $this->_disableSubmitButton = false;
                $_submitButtonClass = '';
                break;
            }
        }
        if ($this->getOrder()->getForcedDoShipmentWithInvoice()) {
            $_submitLabel = Mage::helper('sales')->__('Submit Invoice and Shipment');
        } else {
            $_submitLabel = Mage::helper('sales')->__('Submit Invoice');
        }
        $this->setChild(
            'submit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => $_submitLabel,
                'class'     => 'save submit-button' . $_submitButtonClass,
                'onclick'   => 'disableElements(\'submit-button\');$(\'edit_form\').submit()',
                'disabled'  => $this->_disableSubmitButton
            ])
        );

        return parent::_prepareLayout();
    }

    /**
     * Get is submit button disabled or not
     *
     * @return bool
     */
    function getDisableSubmitButton()
    {
        return $this->_disableSubmitButton;
    }

    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    function getOrder()
    {
        return $this->getInvoice()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve order totals block settings
     *
     * @return array
     */
    function getOrderTotalData()
    {
        return [];
    }

    /**
     * Retrieve order totalbar block data
     *
     * @return array
     */
    function getOrderTotalbarData()
    {
        $totalbarData = [];
        $this->setPriceDataObject($this->getInvoice()->getOrder());
        $totalbarData[] = [Mage::helper('sales')->__('Paid Amount'), $this->displayPriceAttribute('amount_paid'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Refund Amount'), $this->displayPriceAttribute('amount_refunded'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Shipping Amount'), $this->displayPriceAttribute('shipping_captured'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Shipping Refund'), $this->displayPriceAttribute('shipping_refunded'), false];
        $totalbarData[] = [Mage::helper('sales')->__('Order Grand Total'), $this->displayPriceAttribute('grand_total'), true];

        return $totalbarData;
    }

    function formatPrice($price)
    {
        return $this->getInvoice()->getOrder()->formatPrice($price);
    }

    function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', ['order_id' => $this->getInvoice()->getOrderId()]);
    }

    /**
     * Check shipment availability for current invoice
     *
     * @return bool
     */
    function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->getQtyToShip()) {
                return true;
            }
        }
        return false;
    }

    function canEditQty()
    {
        if ($this->getInvoice()->getOrder()->getPayment()->canCapture()) {
            return $this->getInvoice()->getOrder()->getPayment()->canCapturePartial();
        }
        return true;
    }

    /**
     * Check if capture operation is allowed in ACL
     * @return bool
     */
    function isCaptureAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/capture');
    }

    /**
     * Check if invoice can be captured
     * @return bool
     */
    function canCapture()
    {
        return $this->getInvoice()->canCapture();
    }

    /**
     * Check if gateway is associated with invoice order
     * @return bool
     */
    function isGatewayUsed()
    {
        return $this->getInvoice()->getOrder()->getPayment()->getMethodInstance()->isGateway();
    }

    function canSendInvoiceEmail()
    {
        return Mage::helper('sales')->canSendNewInvoiceEmail($this->getOrder()->getStore()->getId());
    }
}
