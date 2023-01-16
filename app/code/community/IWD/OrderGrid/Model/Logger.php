<?php

/**
 * Class IWD_OrderGrid_Model_Logger
 */
class IWD_OrderGrid_Model_Logger
{
    /**
     * @param $message
     * @param bool|false $sessionError
     */
    public static function log($message, $sessionError = false)
    {
        Mage::log($message, null, 'iwd_order_manager.log');

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
