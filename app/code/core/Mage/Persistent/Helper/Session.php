<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Persistent Shopping Cart Data Helper
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Persistent_Helper_Session extends Mage_Core_Helper_Data
{
    protected $_moduleName = 'Mage_Persistent';

    /**
     * Instance of Session Model
     *
     * @var Mage_Persistent_Model_Session|null
     */
    protected $_sessionModel;

    /**
     * Persistent customer
     *
     * @var Mage_Customer_Model_Customer|null
     */
    protected $_customer;

    /**
     * Is "Remember Me" checked
     *
     * @var bool|null
     */
    protected $_isRememberMeChecked;

    /**
     * Get Session model
     *
     * @return Mage_Persistent_Model_Session
     */
    function getSession()
    {
        if (is_null($this->_sessionModel)) {
            $this->_sessionModel = Mage::getModel('persistent/session');
            $this->_sessionModel->loadByCookieKey();
        }
        return $this->_sessionModel;
    }

    /**
     * Force setting session model
     *
     * @param Mage_Persistent_Model_Session|null $sessionModel null to unset session
     * @return Mage_Persistent_Model_Session|null
     */
    function setSession($sessionModel)
    {
        $this->_sessionModel = $sessionModel;
        return $this->_sessionModel;
    }

    /**
     * Check whether persistent mode is running
     *
     * @return bool
     */
    function isPersistent()
    {
        return $this->getSession()->getId() && Mage::helper('persistent')->isEnabled();
    }

    /**
     * Check if "Remember Me" checked
     *
     * @return bool
     */
    function isRememberMeChecked()
    {
        if (is_null($this->_isRememberMeChecked)) {
            //Try to get from checkout session
            $isRememberMeChecked = Mage::getSingleton('checkout/session')->getRememberMeChecked();
            if (!is_null($isRememberMeChecked)) {
                $this->_isRememberMeChecked = $isRememberMeChecked;
                Mage::getSingleton('checkout/session')->unsRememberMeChecked();
                return $isRememberMeChecked;
            }

            /** @var Mage_Persistent_Helper_Data $helper */
            $helper = Mage::helper('persistent');
            return $helper->isEnabled() && $helper->isRememberMeEnabled() && $helper->isRememberMeCheckedDefault();
        }

        return (bool)$this->_isRememberMeChecked;
    }

    /**
     * Set "Remember Me" checked or not
     *
     * @param bool $checked
     */
    function setRememberMeChecked($checked = true)
    {
        $this->_isRememberMeChecked = $checked;
    }

    /**
     * Return persistent customer
     *
     * @return Mage_Customer_Model_Customer
     */
    function getCustomer()
    {
        if (is_null($this->_customer)) {
            $customerId = $this->getSession()->getCustomerId();
            $this->_customer = Mage::getModel('customer/customer')->load($customerId);
        }
        return $this->_customer;
    }
}
