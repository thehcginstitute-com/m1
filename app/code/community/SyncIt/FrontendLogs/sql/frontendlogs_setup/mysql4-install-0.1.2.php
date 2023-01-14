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

    $installer = $this;
    $installer->startSetup();
    $table = $installer->getConnection()
        ->newTable($installer->getTable('frontendlogs/authentication'))
        ->addColumn("log_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'auto_increment' => true,
            'unsigned' => true,
            'identity' => true,
            'nullable' => false,
            'primary' => true,
            'unique' => true,
            ), 'ID')
        ->addColumn("log_date", Varien_Db_Ddl_Table::TYPE_DATETIME, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'Log Date')
        ->addColumn("action_name", Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'Actions')
        ->addColumn("message", Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'Log Message')
        ->addColumn("customer_id", Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'Customer ID')
        ->addColumn("email", Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'Email')
        ->addColumn("client_ip", Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'IP Adress')
        ->addColumn("user_agent", Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'nullable' => true,
            'default' => null,
            'primary' => false,
            'unique' => false,
        ), 'User Agent');

    $installer->getConnection()->createTable($table);
    $installer->endSetup();