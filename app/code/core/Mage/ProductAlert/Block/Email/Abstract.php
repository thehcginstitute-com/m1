<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Alert Abstract Email Block
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_ProductAlert_Block_Email_Abstract extends Mage_Core_Block_Template
{
    /**
     * Product collection array
     *
     * @var array
     */
    protected $_products = [];

    /**
     * Current Store scope object
     *
     * @var Mage_Core_Model_Store|null
     */
    protected $_store;

    /**
     * Set Store scope
     *
     * @param int|string|Mage_Core_Model_Website|Mage_Core_Model_Store $store
     * @return Mage_ProductAlert_Block_Email_Abstract
     */
    function setStore($store)
    {
        if ($store instanceof Mage_Core_Model_Website) {
            $store = $store->getDefaultStore();
        }
        if (!$store instanceof Mage_Core_Model_Store) {
            $store = Mage::app()->getStore($store);
        }

        $this->_store = $store;

        return $this;
    }

    /**
     * Retrieve current store object
     *
     * @return Mage_Core_Model_Store
     */
    function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param double $price
     * @param bool $format             Format price to currency format
     * @param bool $includeContainer   Enclose into <span class="price"><span>
     * @return double
     */
    function formatPrice($price, $format = true, $includeContainer = true)
    {
        return $this->getStore()->convertPrice($price, $format, $includeContainer);
    }

    /**
     * Reset product collection
     *
     */
    function reset()
    {
        $this->_products = [];
    }

    /**
     * Add product to collection
     *
     * @param Mage_Catalog_Model_Product $product
     */
    function addProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_products[$product->getId()] = $product;
    }

    /**
     * Retrieve product collection array
     *
     * @return array
     */
    function getProducts()
    {
        return $this->_products;
    }

    /**
     * Get store url params
     *
     * @return array
     */
    protected function _getUrlParams()
    {
        return [
            '_store'        => $this->getStore(),
            '_store_to_url' => true
        ];
    }

    /**
     * Get filtered product short description to be inserted into mail
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string|null
     */
    function _getFilteredProductShortDescription(Mage_Catalog_Model_Product $product)
    {
        $shortDescription = $product->getShortDescription();
        if ($shortDescription) {
            $shortDescription = Mage::getSingleton('core/input_filter_maliciousCode')->filter($shortDescription);
        }
        return $shortDescription;
    }
}
