<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer dashboard addresses section
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Block_Account_Dashboard_Address extends Mage_Core_Block_Template
{
    /**
     * @return Mage_Customer_Model_Customer
     */
    function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    /**
     * @return string|null
     */
    function getPrimaryShippingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryShippingAddress();

        if ($address instanceof Varien_Object) {
            return $address->format('html');
        } else {
            return Mage::helper('customer')->__('You have not set a default shipping address.');
        }
    }

    /**
     * @return string|null
     */
    function getPrimaryBillingAddressHtml()
    {
        $address = $this->getCustomer()->getPrimaryBillingAddress();

        if ($address instanceof Varien_Object) {
            return $address->format('html');
        } else {
            return Mage::helper('customer')->__('You have not set a default billing address.');
        }
    }

    /**
     * @return string
     */
    function getPrimaryShippingAddressEditUrl()
    {
        return Mage::getUrl('customer/address/edit', ['id' => $this->getCustomer()->getDefaultShipping()]);
    }

    /**
     * @return string
     */
    function getPrimaryBillingAddressEditUrl()
    {
        return Mage::getUrl('customer/address/edit', ['id' => $this->getCustomer()->getDefaultBilling()]);
    }

    /**
     * @return string
     */
    function getAddressBookUrl()
    {
        return $this->getUrl('customer/address/');
    }
}
