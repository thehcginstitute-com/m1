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
 * Adminhtml shipment items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    /**
     * Retrieve invoice order
     *
     * @return Mage_Sales_Model_Order
     */
    function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Mage_Sales_Model_Order_Shipment
     */
    function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    /**
     * Prepare child blocks
     */
    protected function _beforeToHtml()
    {
        $this->setChild(
            'submit_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')->setData([
                'label'     => Mage::helper('sales')->__('Submit Shipment'),
                'class'     => 'save submit-button',
                'onclick'   => 'submitShipment(this);',
            ])
        );

        return parent::_beforeToHtml();
    }

    /**
     * Format given price
     *
     * @param float $price
     * @return string
     */
    function formatPrice($price)
    {
        return $this->getShipment()->getOrder()->formatPrice($price);
    }

    /**
     * Retrieve HTML of update button
     *
     * @return string
     */
    function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    /**
     * Get url for update
     *
     * @return string
     */
    function getUpdateUrl()
    {
        return $this->getUrl('*/*/updateQty', ['order_id' => $this->getShipment()->getOrderId()]);
    }

    /**
     * Check possibility to send shipment email
     *
     * @return bool
     */
    function canSendShipmentEmail()
    {
        return Mage::helper('sales')->canSendNewShipmentEmail($this->getOrder()->getStore()->getId());
    }

	# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the shipping labels feature because it is unused": https://github.com/thehcginstitute-com/m1/issues/375
}
