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
 * Class Mage_Customer_Block_Widget_Name
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method bool getForceUseCustomerAttributes()
 * @method bool getForceUseCustomerRequiredAttributes()
 */
class Mage_Customer_Block_Widget_Name extends Mage_Customer_Block_Widget_Abstract
{
    function _construct()
    {
        parent::_construct();

        // default template location
        $this->setTemplate('customer/widget/name.phtml');
    }

    /**
     * Can show config value
     *
     * @param string $key
     * @return bool
     */
    protected function _showConfig($key)
    {
        return (bool)$this->getConfig($key);
    }

    /**
     * Can show prefix
     *
     * @return bool
     */
    function showPrefix()
    {
        return (bool)$this->_getAttribute('prefix')->getIsVisible();
    }

    /**
     * Define if prefix attribute is required
     *
     * @return bool
     */
    function isPrefixRequired()
    {
        return (bool)$this->_getAttribute('prefix')->getIsRequired();
    }

    /**
     * Retrieve name prefix drop-down options
     *
     * @return array|bool
     */
    function getPrefixOptions()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        $prefixOptions = $helper->getNamePrefixOptions();
        if ($this->getObject() && !empty($prefixOptions)) {
            $oldPrefix = $this->escapeHtml(trim($this->getObject()->getPrefix()));
            $prefixOptions[$oldPrefix] = $oldPrefix;
        }
        return $prefixOptions;
    }

    /**
     * Define if middle name attribute can be shown
     *
     * @return bool
     */
    function showMiddlename()
    {
        return (bool)$this->_getAttribute('middlename')->getIsVisible();
    }

    /**
     * Define if middlename attribute is required
     *
     * @return bool
     */
    function isMiddlenameRequired()
    {
        return (bool)$this->_getAttribute('middlename')->getIsRequired();
    }

    /**
     * Define if suffix attribute can be shown
     *
     * @return bool
     */
    function showSuffix()
    {
        return (bool)$this->_getAttribute('suffix')->getIsVisible();
    }

    /**
     * Define if suffix attribute is required
     *
     * @return bool
     */
    function isSuffixRequired()
    {
        return (bool)$this->_getAttribute('suffix')->getIsRequired();
    }

    /**
     * Retrieve name suffix drop-down options
     *
     * @return array|bool
     */
    function getSuffixOptions()
    {
        /** @var Mage_Customer_Helper_Data $helper */
        $helper = $this->helper('customer');
        $suffixOptions = $helper->getNameSuffixOptions();
        if ($this->getObject() && !empty($suffixOptions)) {
            $oldSuffix = $this->escapeHtml(trim($this->getObject()->getSuffix()));
            $suffixOptions[$oldSuffix] = $oldSuffix;
        }
        return $suffixOptions;
    }

    /**
     * Class name getter
     *
     * @return string
     */
    function getClassName()
    {
        if (!$this->hasData('class_name')) {
            $this->setData('class_name', 'customer-name');
        }
        return $this->getData('class_name');
    }

    /**
     * Container class name getter
     *
     * @return string
     */
    function getContainerClassName()
    {
        $class = $this->getClassName();
        $class .= $this->showPrefix() ? '-prefix' : '';
        $class .= $this->showMiddlename() ? '-middlename' : '';
        $class .= $this->showSuffix() ? '-suffix' : '';
        return $class;
    }

    /**
     * Retrieve customer or customer address attribute instance
     *
     * @param string $attributeCode
     * @return Mage_Customer_Model_Attribute|false
     */
    protected function _getAttribute($attributeCode)
    {
        if ($this->getForceUseCustomerAttributes() || $this->getObject() instanceof Mage_Customer_Model_Customer) {
            return parent::_getAttribute($attributeCode);
        }

        $attribute = Mage::getSingleton('eav/config')->getAttribute('customer_address', $attributeCode);

        if ($this->getForceUseCustomerRequiredAttributes() && $attribute && !$attribute->getIsRequired()) {
            $customerAttribute = parent::_getAttribute($attributeCode);
            if ($customerAttribute && $customerAttribute->getIsRequired()) {
                $attribute = $customerAttribute;
            }
        }

        return $attribute;
    }

    /**
     * Retrieve store attribute label
     *
     * @param string $attributeCode
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    function getStoreLabel($attributeCode)
    {
        $attribute = $this->_getAttribute($attributeCode);
        return $attribute ? $this->__($attribute->getStoreLabel()) : '';
    }
}
