<?php

/**
 * Class IWD_OrderManager_Model_System_Config_Tax_Notification
 */
class IWD_OrderManager_Model_System_Config_Tax_Notification extends Mage_Core_Model_Config_Data
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Factory
     */
    protected $_factory;

    /**
     * Initialize class instance
     *
     * @param array $args
     */
    function __construct(array $args = array())
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
        parent::__construct($args);
    }

    /**
     * Get config model
     *
     * @return Mage_Core_Model_Config_Data
     */
    protected function _getConfig()
    {
        return $this->_factory->getModel('core/config_data');
    }

    /**
     * Prepare and store cron settings after save
     *
     * @return Mage_Tax_Model_Config_Notification
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged()) {
            $this->_resetNotificationFlag(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_DISCOUNT);
            $this->_resetNotificationFlag(Mage_Tax_Model_Config::XML_PATH_TAX_NOTIFICATION_PRICE_DISPLAY);
        }
        return parent::_afterSave();
    }

    /**
     * Reset flag for showing tax notifications
     *
     * @param string $path
     * @return Mage_Tax_Model_Config_Notification
     */
    protected function _resetNotificationFlag($path)
    {
        $this->_getConfig()
            ->load($path, 'path')
            ->setValue(0)
            ->setPath($path)
            ->save();
        return $this;
    }
}
