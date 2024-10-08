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
 * Multishipping checkout select billing address
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Multishipping_Address_Select extends Mage_Checkout_Block_Multishipping_Abstract
{
    /**
     * @return Mage_Checkout_Block_Multishipping_Abstract
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('checkout')->__('Change Billing Address') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return Mage_Checkout_Model_Type_Multishipping|Mage_Core_Model_Abstract
     */
    protected function _getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * @return Mage_Customer_Model_Address[]|mixed
     */
    function getAddressCollection()
    {
        $collection = $this->getData('address_collection');
        if (is_null($collection)) {
            $collection = $this->_getCheckout()->getCustomer()->getAddresses();
            $this->setData('address_collection', $collection);
        }
        return $collection;
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    function isAddressDefaultBilling($address)
    {
        return $address->getId() == $this->_getCheckout()->getCustomer()->getDefaultBilling();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    function isAddressDefaultShipping($address)
    {
        return $address->getId() == $this->_getCheckout()->getCustomer()->getDefaultShipping();
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    function getEditAddressUrl($address)
    {
        return $this->getUrl('*/*/editAddress', ['id' => $address->getId()]);
    }

    /**
     * @param Mage_Sales_Model_Quote_Address $address
     * @return string
     */
    function getSetAddressUrl($address)
    {
        return $this->getUrl('*/*/setBilling', ['id' => $address->getId()]);
    }

    /**
     * @return string
     */
    function getAddNewUrl()
    {
        return $this->getUrl('*/*/newBilling');
    }

    /**
     * @return string
     */
    function getBackUrl()
    {
        return $this->getUrl('*/multishipping/billing');
    }
}
