<?php

/**
 * Class IWD_MultiInventory_Helper_Data
 */
class IWD_MultiInventory_Helper_Data extends Mage_Core_Helper_Data
{
    /**#@+
     * Config xml path
     */
    const CONFIG_XML_PATH_MULTI_INVENTORY_ENABLED = 'iwd_ordermanager/multi_inventory/enable';
    /**#@-*/

    /**
     * @var string
     */
    protected $_version = 'EE';

    /**
     * @return bool
     */
    public function isGridExport()
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
    public function getExtensionVersion()
    {
        return Mage::getConfig()->getModuleConfig("IWD_MultiInventory")->version;
    }

    /**
     * @return bool
     */
    public function isEnterpriseMagentoEdition()
    {
        return ($this->getEdition() == 'Enterprise');
    }

    /**
     * @return bool
     */
    public function isAvailableVersion()
    {
        return !($this->isEnterpriseMagentoEdition() && $this->_version == 'CE');
    }

    /**
     * @return string
     */
    public function getEdition()
    {
        $mage = new Mage();
        $edition = (!is_callable(array($mage, 'getEdition'))) ? 'Community' : Mage::getEdition();
        unset($mage);

        return $edition;
    }

    /**
     * @return mixed
     */
    public function getCurrentIpAddress()
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
    public function getDataTimeFormat()
    {
        return 'm-d-Y H:i:s';
    }

    /**
     * @param $date
     * @return string
     */
    public function getDateTime($date)
    {
        $storeId = null;
        $timezone = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE, $storeId);
        $locale = new Zend_Locale(Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $storeId));
        $date = new Zend_Date(strtotime($date), null, $locale);
        $date->setTimezone($timezone);
        return $date->get('MM-dd-Y H:m:s');
    }

    /**
     * @return bool
     */
    public function isMultiInventoryEnable()
    {
        return Mage::getStoreConfig(self::CONFIG_XML_PATH_MULTI_INVENTORY_ENABLED)
            && $this->isMultiInventoryAllowed();
    }

    /**
     * @return bool
     */
    public function isMultiInventoryAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('catalog/iwd_multiinventory/enabled');
    }

    /**
     * @param $exclPrice
     * @param $inclPrice
     * @return float|int
     */
    public function getRoundPercent($exclPrice, $inclPrice)
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

    public function testaction()
    {
        return 'test-class';
    }
}
