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
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated after 1.4.1.0
 * @see Mage_ProductAlert_Block_Product_View
 */
class Mage_ProductAlert_Block_Stock extends Mage_Core_Block_Template
{
    function __construct()
    {
        parent::__construct();
        $this->setTemplate('productalert/stock.phtml');
    }

    /**
     * @return bool
     */
    function isShow()
    {
        if (!Mage::getStoreConfig('catalog/productalert/allow_stock')) {
            return false;
        }

        if (!$product = Mage::helper('productalert')->getProduct()) {
            return false;
        }
        /** @var Mage_Catalog_Model_Product $product */

        return !$product->isSaleable();
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    function getUrl($route = '', $params = [])
    {
        return Mage::helper('productalert')->getSaveUrl('stock');
    }
}
