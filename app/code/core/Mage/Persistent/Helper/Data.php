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
class Mage_Persistent_Helper_Data extends Mage_Core_Helper_Data
{
    public const XML_PATH_ENABLED = 'persistent/options/enabled';
    public const XML_PATH_LIFE_TIME = 'persistent/options/lifetime';
    public const XML_PATH_LOGOUT_CLEAR = 'persistent/options/logout_clear';
    public const XML_PATH_REMEMBER_ME_ENABLED = 'persistent/options/remember_enabled';
    public const XML_PATH_REMEMBER_ME_DEFAULT = 'persistent/options/remember_default';
    public const XML_PATH_PERSIST_SHOPPING_CART = 'persistent/options/shopping_cart';

    public const LOGGED_IN_LAYOUT_HANDLE = 'customer_logged_in_psc_handle';
    public const LOGGED_OUT_LAYOUT_HANDLE = 'customer_logged_out_psc_handle';

    protected $_moduleName = 'Mage_Persistent';

    /**
     * Name of config file
     *
     * @var string
     */
    protected $_configFileName = 'persistent.xml';

    /**
     * Checks whether Persistence Functionality is enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    function isEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ENABLED, $store);
    }

    /**
     * Checks whether "Remember Me" enabled
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    function isRememberMeEnabled($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_ENABLED, $store);
    }

    /**
     * Is "Remember Me" checked by default
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    function isRememberMeCheckedDefault($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_REMEMBER_ME_DEFAULT, $store);
    }

    /**
     * Is shopping cart persist
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return bool
     */
    function isShoppingCartPersist($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_PERSIST_SHOPPING_CART, $store);
    }

    /**
     * Get Persistence Lifetime
     *
     * @param int|string|Mage_Core_Model_Store $store
     * @return int
     */
    function getLifeTime($store = null)
    {
        $lifeTime = (int) Mage::getStoreConfig(self::XML_PATH_LIFE_TIME, $store);
        return ($lifeTime < 0) ? 0 : $lifeTime;
    }

    /**
     * Check if set `Clear on Logout` in config settings
     *
     * @return bool
     */
    function getClearOnLogout()
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_LOGOUT_CLEAR);
    }

    /**
     * Retrieve url for unset long-term cookie
     *
     * @return string
     */
    function getUnsetCookieUrl()
    {
        return $this->_getUrl('persistent/index/unsetCookie');
    }

    /**
     * Retrieve name of persistent customer
     *
     * @return string
     */
    function getPersistentName()
    {
        return $this->__('(Not %s?)', $this->escapeHtml(Mage::helper('persistent/session')->getCustomer()->getName()));
    }

    /**
     * Retrieve path for config file
     *
     * @return string
     */
    function getPersistentConfigFilePath()
    {
        return Mage::getConfig()->getModuleDir('etc', $this->_getModuleName()) . DS . $this->_configFileName;
    }

    /**
     * Check whether specified action should be processed
     *
     * @param Varien_Event_Observer $observer
     * @return bool
     */
    function canProcess($observer)
    {
        $action = $observer->getEvent()->getAction();
        $controllerAction = $observer->getEvent()->getControllerAction();

        if ($action instanceof Mage_Core_Controller_Varien_Action) {
            return !$action->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }
        if ($controllerAction instanceof Mage_Core_Controller_Varien_Action) {
            return !$controllerAction->getFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_START_SESSION);
        }
        return true;
    }

    /**
     * Get create account url depends on checkout
     *
     * @param string $url
     * @return string
     */
    function getCreateAccountUrl($url)
    {
        if (Mage::helper('checkout')->isContextCheckout()) {
            $url = Mage::helper('core/url')->addRequestParam($url, ['context' => 'checkout']);
        }
        return $url;
    }
}
