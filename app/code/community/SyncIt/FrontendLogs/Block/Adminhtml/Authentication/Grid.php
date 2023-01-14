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

class SyncIt_FrontendLogs_Block_Adminhtml_Authentication_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId("authenticationGrid");
        $this->setDefaultSort("log_id");
        $this->setDefaultDir("DESC");
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel("frontendlogs/authentication")->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn("log_id", array(
            "header" => Mage::helper("frontendlogs")->__("ID"),
            "width" => "50px",
            "type" => "number",
            "index" => "log_id",
        ));
        $this->addColumn('log_date', array(
            'header'    => Mage::helper('frontendlogs')->__('Log Date'),
            'index'     => 'log_date',
            'type'      => 'datetime',
        ));
        $this->addColumn("customer_id", array(
            "header" => Mage::helper("frontendlogs")->__("Customer ID"),
            "width" => "20px",
            "index" => "customer_id",
        ));
        $this->addColumn("email", array(
            "header" => Mage::helper("frontendlogs")->__("Email"),
            "index" => "email",
        ));
        $this->addColumn("action_name", array(
            "header" => Mage::helper("frontendlogs")->__("Actions"),
            "index" => "action_name",
        ));
        $this->addColumn("message", array(
            "header" => Mage::helper("frontendlogs")->__("Log Message"),
            "index" => "message",
        ));
        $this->addColumn("client_ip", array(
            "header" => Mage::helper("frontendlogs")->__("IP Address"),
            "index" => "client_ip",
        ));
        $this->addColumn("user_agent", array(
            "header" => Mage::helper("frontendlogs")->__("User Agent"),
            "index" => "user_agent",
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel'));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
       return 'Log details';
    }
}