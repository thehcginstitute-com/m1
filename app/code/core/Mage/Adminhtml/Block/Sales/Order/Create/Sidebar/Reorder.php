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
 * Adminhtml sales order create sidebar cart block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Reorder extends Mage_Adminhtml_Block_Sales_Order_Create_Sidebar_Abstract
{
    /**
     * Storage action on selected item
     *
     * @var string
     */
    protected $_sidebarStorageAction = 'add_order_item';

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_sidebar_reorder');
        $this->setDataId('reorder');
    }

    /**
     * @return string
     */
    function getHeaderText()
    {
        return Mage::helper('sales')->__('Last Ordered Items');
    }

    /**
     * Retrieve last order on current website
     *
     * @return Mage_Sales_Model_Order|false
     */
    function getLastOrder()
    {
        $storeIds = $this->getQuote()->getStore()->getWebsite()->getStoreIds();
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToFilter('customer_id', $this->getCustomerId())
            ->addFieldToFilter('store_id', ['in' => $storeIds])
            ->setOrder('created_at', 'desc')
            ->setPageSize(1)
            ->load();
        foreach ($collection as $order) {
            return $order;
        }

        return false;
    }
    /**
     * Retrieve item collection
     *
     * @return array|false
     */
    function getItemCollection()
    {
        if ($order = $this->getLastOrder()) {
            $items = [];
            foreach ($order->getItemsCollection() as $item) {
                if (!$item->getParentItem()) {
                    $items[] = $item;
                }
            }
            return $items;
        }
        return false;
    }

    /**
     * @return false
     */
    function canDisplayItemQty()
    {
        return false;
    }

    /**
     * @return false
     */
    function canRemoveItems()
    {
        return false;
    }

    /**
     * @return false
     */
    function canDisplayPrice()
    {
        return false;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param Varien_Object $item
     * @return int
     */
    function getIdentifierId($item)
    {
        return $item->getId();
    }
}
