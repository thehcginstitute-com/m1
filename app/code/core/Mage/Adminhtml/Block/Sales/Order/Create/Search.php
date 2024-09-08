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
 * Adminhtml sales order create search block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Search extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_search');
    }

    /**
     * @return string
     */
    function getHeaderText()
    {
        return Mage::helper('sales')->__('Please Select Products to Add');
    }

    /**
     * @return string
     */
    function getButtonsHtml()
    {
        $addButtonData = [
            'label' => Mage::helper('sales')->__('Add Selected Product(s) to Order'),
            'onclick' => 'order.productGridAddSelected()',
            'class' => 'add',
        ];
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setData($addButtonData)->toHtml();
    }

    /**
     * @return string
     */
    function getHeaderCssClass()
    {
        return 'head-catalog-product';
    }
}
