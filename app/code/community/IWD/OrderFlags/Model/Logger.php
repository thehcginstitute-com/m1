<?php

/**
 * Class IWD_OrderFlags_Model_Logger
 */
class IWD_OrderFlags_Model_Logger
{
    /**
     * @param $message
     * @param bool|false $sessionError
     */
    public static function log($message, $sessionError = false)
    {
        Mage::log($message, null, 'iwd_order_flags.log');

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
