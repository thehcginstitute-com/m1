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
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Multishipping checkout choose item addresses block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Multishipping_Addresses extends Mage_Sales_Block_Items_Abstract
{
    /**
     * Retrieve multishipping checkout model
     *
     * @return Mage_Checkout_Model_Type_Multishipping
     */
    function getCheckout()
    {
        return Mage::getSingleton('checkout/type_multishipping');
    }

    /**
     * @return Mage_Sales_Block_Items_Abstract
     */
    protected function _prepareLayout()
    {
        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle(Mage::helper('checkout')->__('Ship to Multiple Addresses') . ' - ' . $headBlock->getDefaultTitle());
        }
        return parent::_prepareLayout();
    }

    /**
     * @return array
     * @throws Exception
     */
    function getItems()
    {
        $items = $this->getCheckout()->getQuoteShippingAddressesItems();
        $itemsFilter = new Varien_Filter_Object_Grid();
        $itemsFilter->addFilter(new Varien_Filter_Sprintf('%d'), 'qty');
        return $itemsFilter->filter($items);
    }

    /**
     * Retrieve HTML for addresses dropdown
     *
     * @param Mage_Sales_Model_Quote_Address_Item $item
     * @param string $index
     * @return string
     */
    function getAddressesHtmlSelect($item, $index)
    {
        $select = $this->getLayout()->createBlock('core/html_select')
            ->setName('ship[' . $index . '][' . $item->getQuoteItemId() . '][address]')
            ->setId('ship_' . $index . '_' . $item->getQuoteItemId() . '_address')
            ->setValue($item->getCustomerAddressId())
            ->setOptions($this->getAddressOptions());

        return $select->getHtml();
    }

    /**
     * Retrieve options for addresses dropdown
     *
     * @return array
     */
    function getAddressOptions()
    {
        $options = $this->getData('address_options');
        if (is_null($options)) {
            $options = [];
            foreach ($this->getCustomer()->getAddresses() as $address) {
                $options[] = [
                    'value' => $address->getId(),
                    'label' => $address->format('oneline')
                ];
            }
            $this->setData('address_options', $options);
        }

        return $options;
    }

    /**
     * @return Mage_Customer_Model_Customer
     */
    function getCustomer()
    {
        return $this->getCheckout()->getCustomerSession()->getCustomer();
    }

    /**
     * @param Varien_Object $item
     * @return string
     */
    function getItemUrl($item)
    {
        return $this->getUrl('catalog/product/view/id/' . $item->getProductId());
    }

    /**
     * @param Varien_Object $item
     * @return string
     */
    function getItemDeleteUrl($item)
    {
        return $this->getUrl('*/*/removeItem', ['address' => $item->getQuoteAddressId(), 'id' => $item->getId()]);
    }

    /**
     * @return string
     */
    function getPostActionUrl()
    {
        return $this->getUrl('*/*/addressesPost');
    }

    /**
     * @return string
     */
    function getNewAddressUrl()
    {
        return Mage::getUrl('*/multishipping_address/newShipping');
    }

    /**
     * @return string
     */
    function getBackUrl()
    {
        return Mage::getUrl('*/cart/');
    }

    /**
     * @return bool
     */
    function isContinueDisabled()
    {
        return !$this->getCheckout()->validateMinimumAmount();
    }
}
