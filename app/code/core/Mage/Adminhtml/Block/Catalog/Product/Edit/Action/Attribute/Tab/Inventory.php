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
 * Products mass update inventory tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Edit_Action_Attribute_Tab_Inventory extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Retrieve Backorders Options
     *
     * @return array
     */
    function getBackordersOption()
    {
        return Mage::getSingleton('cataloginventory/source_backorders')->toOptionArray();
    }

    /**
     * Retrieve field suffix
     *
     * @return string
     */
    function getFieldSuffix()
    {
        return 'inventory';
    }

    /**
     * Retrieve current store id
     *
     * @return int
     */
    function getStoreId()
    {
        $storeId = $this->getRequest()->getParam('store');
        return (int) $storeId;
    }

    /**
     * Get default config value
     *
     * @param string $field
     * @return mixed
     */
    function getDefaultConfigValue($field)
    {
        return Mage::getStoreConfig(Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . $field, $this->getStoreId());
    }

    /**
     * ######################## TAB settings #################################
     */
    function getTabLabel()
    {
        return Mage::helper('catalog')->__('Inventory');
    }

    function getTabTitle()
    {
        return Mage::helper('catalog')->__('Inventory');
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
