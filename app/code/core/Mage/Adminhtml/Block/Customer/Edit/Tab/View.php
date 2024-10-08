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
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer account form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tab_View extends Mage_Adminhtml_Block_Template implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer;

    protected $_customerLog;

    /**
     * @return Mage_Customer_Model_Customer
     */
    function getCustomer()
    {
        if (!$this->_customer) {
            $this->_customer = Mage::registry('current_customer');
        }
        return $this->_customer;
    }

    /**
     * @return string|void
     */
    function getGroupName()
    {
        if ($groupId = $this->getCustomer()->getGroupId()) {
            return Mage::getModel('customer/group')
                ->load($groupId)
                ->getCustomerGroupCode();
        }
    }

    /**
     * Load Customer Log model
     *
     * @return Mage_Log_Model_Customer
     */
    function getCustomerLog()
    {
        if (!$this->_customerLog) {
            $this->_customerLog = Mage::getModel('log/customer')
                ->loadByCustomer($this->getCustomer()->getId());
        }
        return $this->_customerLog;
    }

    /**
     * Get customer creation date
     *
     * @return string|null
     */
    function getCreateDate()
    {
        if (!$this->getCustomer()->getCreatedAt()) {
            return null;
        }
        return $this->_getCoreHelper()->formatDate(
            $this->getCustomer()->getCreatedAt(),
            Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM,
            true
        );
    }

    /**
     * @return string|null
     */
    function getStoreCreateDate()
    {
        if (!$this->getCustomer()->getCreatedAt()) {
            return null;
        }
        $date = Mage::app()->getLocale()->storeDate(
            $this->getCustomer()->getStoreId(),
            $this->getCustomer()->getCreatedAtTimestamp(),
            true
        );
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
    }

    function getStoreCreateDateTimezone()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
    }

    /**
     * Get customer last login date
     *
     * @return string
     */
    function getLastLoginDate()
    {
        $date = $this->getCustomerLog()->getLoginAtTimestamp();
        if ($date) {
            return Mage::helper('core')->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        }
        return Mage::helper('customer')->__('Never');
    }

    /**
     * @return string
     */
    function getStoreLastLoginDate()
    {
        if ($date = $this->getCustomerLog()->getLoginAtTimestamp()) {
            $date = Mage::app()->getLocale()->storeDate(
                $this->getCustomer()->getStoreId(),
                $date,
                true
            );
            return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM, true);
        }
        return Mage::helper('customer')->__('Never');
    }

    function getStoreLastLoginDateTimezone()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())
            ->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);
    }

    /**
     * @return string
     */
    function getCurrentStatus()
    {
        $log = $this->getCustomerLog();
        if ($log->getLogoutAt()
            || !$log->getLastVisitAt()
            || strtotime(Varien_Date::now()) - strtotime($log->getLastVisitAt()) > Mage_Log_Model_Visitor::getOnlineMinutesInterval() * 60
        ) {
            return Mage::helper('customer')->__('Offline');
        }
        return Mage::helper('customer')->__('Online');
    }

    /**
     * @return string
     */
    function getIsConfirmedStatus()
    {
        $this->getCustomer();
        if (!$this->_customer->getConfirmation()) {
            return Mage::helper('customer')->__('Confirmed');
        }
        if ($this->_customer->isConfirmationRequired()) {
            return Mage::helper('customer')->__('Not confirmed, cannot login');
        }
        return Mage::helper('customer')->__('Not confirmed, can login');
    }

    function getCreatedInStore()
    {
        return Mage::app()->getStore($this->getCustomer()->getStoreId())->getName();
    }

    function getStoreId()
    {
        return $this->getCustomer()->getStoreId();
    }

    /**
     * @return string
     */
    function getBillingAddressHtml()
    {
        $html = '';
        if ($address = $this->getCustomer()->getPrimaryBillingAddress()) {
            $html = $address->format('html');
        } else {
            $html = Mage::helper('customer')->__('The customer does not have default billing address.');
        }
        return $html;
    }

    /**
     * @return string
     */
    function getAccordionHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * @return string
     */
    function getSalesHtml()
    {
        return $this->getChildHtml('sales');
    }

    /**
     * @return string
     */
    function getTabLabel()
    {
        return Mage::helper('customer')->__('Customer View');
    }

    /**
     * @return string
     */
    function getTabTitle()
    {
        return Mage::helper('customer')->__('Customer View');
    }

    /**
     * @return bool
     */
    function canShowTab()
    {
        if (Mage::registry('current_customer')->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    function isHidden()
    {
        if (Mage::registry('current_customer')->getId()) {
            return false;
        }
        return true;
    }

    /**
     * Return instance of core helper
     *
     * @return Mage_Core_Helper_Data
     */
    protected function _getCoreHelper()
    {
        return Mage::helper('core');
    }
}
