<?php

/**
 * Class IWD_OrderGrid_Helper_Data
 */
class IWD_OrderGrid_Helper_Data extends Mage_Core_Helper_Data
{
    /**#@+
     * Config xml path
     */
    const CONFIG_XPATH_CUSTOM_GRID_ENABLE = 'iwd_ordermanager/grid_order/enable';
    const CONFIG_XPATH_NOTIFY_CUSTOMER_CHECKED = 'iwd_ordermanager/edit/notify_checked';
    const CONFIG_XPATH_ORDER_GRID_TOTALS = 'iwd_ordermanager/grid_order/order_totals_enable';
    const CONFIG_XPATH_NOTIFY_CUSTOMER_MASSACTION = 'iwd_ordermanager/grid_order/notify_customer_massaction';
    const CONFIG_XPATH_MASSACTION_SAVE = 'iwd_ordermanager/massaction/order_grid';
    const CONFIG_XPATH_ORDER_TOTALS_SETS = 'iwd_ordermanager/grid_order/order_totals_sets';
    const CONFIG_XPATH_CUSTOMER_RESENT_ORDERS_COUNT = 'iwd_ordermanager/customer_orders/resent_orders_count';
    /**#@-*/

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
     * @return mixed
     */
    function getExtensionVersion()
    {
        return Mage::getConfig()->getModuleConfig("IWD_OrderGrid")->version;
    }

    /**
     * @return mixed
     */
    function isNotifyCustomerCheckedDefault()
    {
        return Mage::getStoreConfig(self::CONFIG_XPATH_NOTIFY_CUSTOMER_CHECKED);
    }

    /**
     * @return string
     */
    function isNotifyCustomerChecked()
    {
        return $this->isNotifyCustomerCheckedDefault() ? 'checked="checked"' : "";
    }

    /**
     * @return bool
     */
    function isEnabled()
    {
        return Mage::getStoreConfig(self::CONFIG_XPATH_CUSTOM_GRID_ENABLE) && $this->isAllowCustomGrid();
    }

    /**
     * @return bool
     */
    function isAllowCustomGrid()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordergrid/custom_grid');
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
     * @return string
     */
    function getDataTimeFormat()
    {
        return 'm-d-Y H:i:s';
    }
}
