<?php

/**
 * Class IWD_MultiInventory_Model_Logger
 */
class IWD_MultiInventory_Model_Logger extends Mage_Core_Model_Abstract
{
    /**
     * @param $message
     * @param bool|string $sessionError
     */
    public static function log($message, $sessionError = false)
    {
        Mage::log($message, null, 'iwd_multi_inventory.log');

        if (!empty($sessionError)) {
            $sessionError = is_string($sessionError) ? $sessionError : $message;

            /**
             * @var $session Mage_Adminhtml_Model_Session
             */
            $session = Mage::getSingleton('adminhtml/session');
            $session->addError($sessionError);
        }
    }
}
