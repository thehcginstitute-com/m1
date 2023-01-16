<?php
$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS `{$this->getTable('wk_customerfields')}` (
	`index_id` int(11) NOT NULL auto_increment,
	`customer_attribute_id` int(11) NOT NULL default '0', 
	`customer_attribute_code` varchar(255) NOT NULL default '0', 
	`inputname` varchar(255) NOT NULL DEFAULT '',
	`sort_order` int(11) NOT NULL,
	`is_in_saif` int(11) NOT NULL,
	`is_in_semail` int(11) NOT NULL,
	`viewinforms` varchar(255) NOT NULL DEFAULT '',
	`allowed_extensions` varchar(255) NOT NULL,
	`dependable_labelname` varchar(255) NOT NULL,
	`dependable_inputname` varchar(255) NOT NULL,
	`dependable_validation_type` varchar(255) NOT NULL,
	`dependable_inputtype` varchar(255) NOT NULL,
	`dependable_selectoption` text NOT NULL,
	`dependable_reuiredfield` smallint(6) NOT NULL,
	`dependable_allowed_extensions` varchar(255) NOT NULL,
	`created_time` datetime DEFAULT NULL,
	`update_time` datetime DEFAULT NULL,
    `status` smallint(6) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`index_id`)
	) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();

