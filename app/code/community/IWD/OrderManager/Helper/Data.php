<?php

/**
 * Class IWD_OrderManager_Helper_Data
 */
class IWD_OrderManager_Helper_Data extends Mage_Core_Helper_Data
{
    /**#@+
     * Config xml path
     */
    const CONFIG_XML_PATH_SHOW_ITEM_IMAGE = 'iwd_ordermanager/edit/show_item_image';
    const CONFIG_XML_CUSTOM_GRID_ENABLE = 'iwd_ordermanager/grid_order/enable';
    const CONFIG_XML_CONFIRM_EDIT_CHECKED = 'iwd_ordermanager/edit/confirm_edit_checked';
    const CONFIG_XML_NOTIFY_CUSTOMER_CHECKED = 'iwd_ordermanager/edit/notify_checked';
    const CONFIG_XML_PATH_RECALCULATE_ORDER_AMOUNT = 'iwd_ordermanager/edit/recalculate_amount_checked';
    const CONFIG_XML_PATH_VALIDATE_INVENTORY = 'iwd_ordermanager/edit/validate_inventory';
    const CONFIG_XML_PATH_MANAGE_CUSTOM_AMOUNT_TAX = 'iwd_ordermanager/edit/manage_custom_amount_tax';
    const CONFIG_XML_PATH_MULTI_INVENTORY_ENABLED = 'iwd_ordermanager/multi_inventory/enable';
    const CONFIG_XML_PATH_DEFERRED_RE_AUTHORIZATION = 'iwd_ordermanager/edit/deferred_re_authorization';
    const CONFIG_XML_PATH_ORDER_OPTIONS_HIDE = 'iwd_ordermanager/order_options/hide';
    const CONFIG_XML_PATH_ORDER_GRID_TOTALS = 'iwd_ordermanager/grid_order/order_totals_enable';
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
     * @return string
     */
    function isRecalculateOrderAmountChecked()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_RECALCULATE_ORDER_AMOUNT, Mage::app()->getStore())
            ? 'checked="checked"'
            : "";
    }

    /**
     * @return int
     */
    function isValidateInventory()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_VALIDATE_INVENTORY, Mage::app()->getStore()) ? 1 : 0;
    }

    /**
     * @return mixed
     */
    function getExtensionVersion()
    {
        return Mage::getConfig()->getModuleConfig("IWD_OrderManager")->version;
    }

    /**
     * @param $table
     * @return bool|string
     */
    function CheckTableEngine($table)
    {
        $cache = Mage::app()->getCache();

        $engine = $cache->load("iwd_order_manager_engine");
        if ($engine !== false) {
            return (bool)$engine;
        }

        try {
            $dbname = (string)Mage::getConfig()->getResourceConnectionConfig('default_setup')->dbname;
            $sql = "SELECT engine FROM `information_schema`.`tables` WHERE `table_schema`='{$dbname}' AND `table_name`='{$table}'";
            $data = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchAll($sql);
			# 2024-02-22 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# «Undefined index: engine in app/code/community/IWD/OrderManager/Helper/Data.php on line 85»:
			# https://github.com/thehcginstitute-com/m1/issues/426
            $isEngineInno = df_bts('InnoDB' === df_first($data[0]));
            $cache->save("{$isEngineInno}", 'iwd_order_manager_engine', ["iwd_order_manager_engine"], 3600);
            return $isEngineInno;
        } catch (Exception $e) {
            IWD_OrderManager_Model_Logger::log($e->getMessage());
        }

        return false;
    }

    /**
     * @return mixed
     */
    function isShowItemImage()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_SHOW_ITEM_IMAGE, Mage::app()->getStore());
    }

    /**
     * @return string
     */
    function isConfirmEditChecked()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_CONFIRM_EDIT_CHECKED) ? 'checked="checked"' : "";
    }

    /**
     * @return mixed
     */
    function isNotifyCustomerCheckedDefault()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_NOTIFY_CUSTOMER_CHECKED);
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
    function enableCustomGrid()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_CUSTOM_GRID_ENABLE) && $this->isAllowCustomGrid();
    }

    /**
     * @return bool
     */
    function isAllowCustomGrid()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager/order/custom_grid');
    }

    /**
     * @return mixed
     */
    function isAllowHideOrders()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_ORDER_OPTIONS_HIDE);
    }

    /**
     * @return bool|string
     */
    function CheckOrderTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_order');
        return $this->CheckTableEngine($table);
    }

    /**
     * @return bool|string
     */
    function CheckCreditmemoTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_creditmemo');
        return $this->CheckTableEngine($table);
    }

    /**
     * @return bool|string
     */
    function CheckInvoiceTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_invoice');
        return $this->CheckTableEngine($table);
    }

    /**
     * @return bool|string
     */
    function CheckShipmentTableEngine()
    {
        $table = Mage::getSingleton('core/resource')->getTableName('sales_flat_shipment');
        return $this->CheckTableEngine($table);
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
     * @return mixed
     */
    function getCurrentIpAddress()
    {
        $ip = $_SERVER['REMOTE_ADDR'];

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $ip;
    }

    /**
     * @return string
     */
    function getDataTimeFormat()
    {
        return 'm-d-Y H:i:s';
    }

    /**
     * @param $date
     * @return string
     */
    function getDateTime($date)
    {
        $storeId = null;
        $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
        $locale = new Zend_Locale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId));
        $date = new Zend_Date(strtotime($date), null, $locale);
        $date->setTimezone($timezone);
        return $date->get('MM-dd-Y H:m:s');
    }

    /**
     * @return mixed
     */
    function isMultiInventoryEnable()
    {
        /**
         * We have this settings in IWD Multi Inventory.
         * It's return false if IWD Multi Inventory does not installed or disabled
         */
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_MULTI_INVENTORY_ENABLED)
            && $this->isMultiInventoryAllowed();
    }

    /**
     * @return bool
     */
    function isMultiInventoryAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/iwd_multiinventory/enabled');
    }

    /**
     * @return bool
     */
    function isAutoReAuthorization()
    {
        return (bool)(int)Mage::getStoreConfig(self::CONFIG_XML_PATH_DEFERRED_RE_AUTHORIZATION);
    }

    /**
     * @param $exclPrice
     * @param $inclPrice
     * @return float|int
     */
    function getRoundPercent($exclPrice, $inclPrice)
    {
        $percent = ($exclPrice != 0) ? ($inclPrice / $exclPrice - 1) * 100 : 0;

        $rates = Mage::getModel('tax/calculation_rate')->getCollection()
            ->addFieldToSelect('rate')
            ->getColumnValues('rate');

        for ($i = 5; $i >= 0; $i--) {
            $roundedPercent = round($percent, $i);
            if (in_array($roundedPercent, $rates)) {
                return $roundedPercent;
            }
        }

        return round($percent, 2);
    }

    /**
     * @return bool
     */
    function isManageTaxForCustomFee()
    {
        return (bool)(int)Mage::getStoreConfig(self::CONFIG_XML_PATH_MANAGE_CUSTOM_AMOUNT_TAX);
    }

    /**
     * @return bool
     */
    function isOrderManagerEnabled()
    {
        return true && $this->isOrderManagerAllowed();
    }

    /**
     * @return bool
     */
    function isOrderManagerAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('iwd_ordermanager');
    }
}
