<?php

/**
 * Class IWD_AdminCheckout_Helper_Data
 */
class IWD_AdminCheckout_Helper_Data extends Mage_Core_Helper_Data
{
    /**#@+
     * Config xml path
     */
    const CONFIG_XML_PATH_CUSTOM_CREATE_PROCESS = 'iwd_ordermanager/crate_process/enable';
    const CONFIG_XML_PATH_DEFAULT_SHIPPING_METHOD = 'iwd_ordermanager/crate_process/default_shipping';
    const CONFIG_XML_PATH_DEFAULT_STORE_VIEW = 'iwd_ordermanager/crate_process/default_store';
    /**#@-*/

    /**
     * @var string
     */
    protected $_version = 'EE';

    /**
     * @return bool
     */
    function isEnterpriseMagentoEdition()
    {
        return ($this->getEdition() == 'Enterprise');
    }

    /**
     * @return bool
     */
    function isAvailableVersion()
    {
        return !($this->isEnterpriseMagentoEdition() && $this->_version == 'CE');
    }

    /**
     * @return string
     */
    function getEdition()
    {
        $mage = new Mage();
        $edition = (!is_callable(array($mage, 'getEdition'))) ? 'Community' : Mage::getEdition();
        unset($mage);

        return $edition;
    }

    /**
     * @return string
     */
    function getExtensionVersion()
    {
        return Mage::getConfig()->getModuleConfig("IWD_AdminCheckout")->version;
    }

    /**
     * @return bool
     */
    function isCustomCreationProcess()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_CUSTOM_CREATE_PROCESS)
            && $this->isCustomCreationProcessAllowed();
    }

    /**
     * @return bool
     */
    function isCustomCreationProcessAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_admin_checkout/enabled');
    }

    /**
     * @return string
     */
    function getDefaultShippingMethod()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DEFAULT_SHIPPING_METHOD);
    }

    /**
     * @return int
     */
    function getDefaultStoreView()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_DEFAULT_STORE_VIEW);
    }
}
