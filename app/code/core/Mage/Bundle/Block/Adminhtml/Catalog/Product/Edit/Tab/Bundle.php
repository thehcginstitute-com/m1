<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product bundle items tab block
 *
 * @category   Mage
 * @package    Mage_Bundle
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_product = null;
    function __construct()
    {
        parent::__construct();
        $this->setSkipGenerateContent(true);
        $this->setTemplate('bundle/product/edit/bundle.phtml');
    }

    /**
     * @return string
     */
    function getTabUrl()
    {
        return $this->getUrl('*/bundle_product_edit/form', ['_current' => true]);
    }

    /**
     * @return string
     */
    function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Prepare layout
     *
     * @inheritDoc
     */
    protected function _prepareLayout()
    {
        $this->setChild(
            'add_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData([
                    'label' => Mage::helper('bundle')->__('Add New Option'),
                    'class' => 'add',
                    'id'    => 'add_new_option',
                    'on_click' => 'bOption.add()'
                ])
        );

        $this->setChild(
            'options_box',
            $this->getLayout()->createBlock(
                'bundle/adminhtml_catalog_product_edit_tab_bundle_option',
                'adminhtml.catalog.product.edit.tab.bundle.option'
            )
        );

        return parent::_prepareLayout();
    }

    /**
     * Check block readonly
     *
     * @return bool
     */
    function isReadonly()
    {
        return $this->getProduct()->getCompositeReadonly();
    }

    /**
     * @return string
     */
    function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * @return string
     */
    function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    /**
     * @return string
     */
    function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * @return Mage_Catalog_Model_Product
     */
    function getProduct()
    {
        return Mage::registry('product');
    }

    /**
     * @return string
     */
    function getTabLabel()
    {
        return Mage::helper('bundle')->__('Bundle Items');
    }

    /**
     * @return string
     */
    function getTabTitle()
    {
        return Mage::helper('bundle')->__('Bundle Items');
    }

    /**
     * @return bool
     */
    function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    function isHidden()
    {
        return false;
    }
}
