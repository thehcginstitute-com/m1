<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Block_Customer_Widget_Abstract extends Mage_Core_Block_Template
{
    function getConfig()
    {
        return $this->helper('magepsycho_customerregfields')->cfgH();
    }

    function getFieldIdFormat()
    {
        if (!$this->hasData('field_id_format')) {
            $this->setData('field_id_format', '%s');
        }
        return $this->getData('field_id_format');
    }

    function getFieldNameFormat()
    {
        if (!$this->hasData('field_name_format')) {
            $this->setData('field_name_format', '%s');
        }
        return $this->getData('field_name_format');
    }

    function getFieldId($field)
    {
        return sprintf($this->getFieldIdFormat(), $field);
    }

    function getFieldName($field)
    {
        return sprintf($this->getFieldNameFormat(), $field);
    }

    /**
     * Retrieve customer attribute instance
     *
     * @param string $attributeCode
     * @return Mage_Customer_Model_Attribute|false
     */
    protected function _getAttribute($attributeCode)
    {
        return Mage::getSingleton('eav/config')->getAttribute('customer', $attributeCode);
    }

    /**
     * Get current customer from session
     *
     * @return Mage_Customer_Model_Customer
     */
    function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    function getGroupLabel()
    {
        return $this->getConfig()->getGroupSelectionLabel();
    }

    /**
     * Check if group field marked as required
     *
     * @return bool
     */
    function isRequired()
    {
        return (bool)$this->getConfig()->isGroupFieldRequired();
    }
}
