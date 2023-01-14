<?php
/**
 * SyncIt Group Ltd.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the SyncIt Group that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.syncitgroup.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to office@syncitgroup.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.syncitgroup.com/ for more information
 * or send an email to office@syncitgroup.com
 *
 * @category   SyncIt
 * @package    SyncIt_Frontend_Logs
 * @copyright  Copyright (c) 2015 SyncIt Group (http://www.syncitgroup.com/)
 * @license    http://www.syncitgroup.com/LICENSE-1.0.html
 */

/**
 * Frontend Logs extension
 *
 * @category   SyncIt
 * @package    SyncIt_Frontend_Logs
 * @author     SyncIt Group Dev Team <support@syncitgroup.com>
 */

class SyncIt_FrontendLogs_Model_Authentication extends Mage_Core_Model_Abstract {

    protected function _construct() {

       $this->_init("frontendlogs/authentication");

    }

    public function writeLogs($LogData) {

        $date = Mage::getModel('core/date')->date('Y-m-d H:i:s');
        $clientIP = Mage::app()->getRequest()->getClientIp();
        $user_agent = isset($_SERVER["HTTP_USER_AGENT"]) ? $_SERVER["HTTP_USER_AGENT"] : '';

        // write to database
        $write = Mage::getModel("frontendlogs/authentication");
        $write
            ->setLogDate($date)
            ->setActionName($LogData['action_name'])
            ->setMessage($LogData['message'])
            ->setCustomerId($LogData['customer_id'])
            ->setEmail($LogData['customer_email'])
            ->setClientIp($clientIP)
            ->setUserAgent($user_agent);

        // save
        $write->save();
    }
}
	 