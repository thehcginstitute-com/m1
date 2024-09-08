<?php

/**
 * Class IWD_OrderFlags_Helper_Data
 */
class IWD_OrderFlags_Helper_Data extends Mage_Core_Helper_Data
{
    const XPATH_EXTENSION_ENABLED = 'iwd_orderflags/order_flags/enable';

    /**
     * @return mixed
     */
    function getExtensionVersion()
    {
        return Mage::getConfig()->getModuleConfig("IWD_OrderFlags")->version;
    }

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
     * @return bool
     */
    function isGridExport()
    {
        /**
         * @var $http Mage_Core_Helper_Http
         */
        $http = Mage::helper('core/http');
        $path = $http->getRequestUri();

        $exportCsv = (strstr($path, 'exportCsv') !== false);
        $exportExcel = (strstr($path, 'exportExcel') !== false);
        return $exportCsv || $exportExcel;
    }

    /**
     * @return bool
     */
    function isEnabled()
    {
        return Mage::getStoreConfig(self::XPATH_EXTENSION_ENABLED) && $this->isAllowOrderFlags();
    }

    /**
     * @return bool
     */
    function isAllowOrderFlags()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_orderflags/assign_flags');
    }

    /**
     * @return bool
     */
    function isIwdOrderGridEnabled()
    {
        try {
            if (class_exists('IWD_OrderGrid_Helper_Data')
                && Mage::helper('core')->isModuleEnabled('IWD_OrderGrid')
            ) {
                return Mage::helper('iwd_ordergrid')->isEnabled();
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
