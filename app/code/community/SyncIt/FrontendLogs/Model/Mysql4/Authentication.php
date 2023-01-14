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

    class SyncIt_FrontendLogs_Model_Mysql4_Authentication extends Mage_Core_Model_Mysql4_Abstract {

        protected function _construct() {
            $this->_init("frontendlogs/authentication", "log_id");
        }
    }