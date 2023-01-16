<?php
$installer = $this;
$installer->startSetup();
$installer->run("

CREATE TABLE IF NOT EXISTS {$this->getTable('wk_customfields')} (
	`id` int(11) unsigned NOT NULL auto_increment,
	`labelname` varchar(255) NOT NULL DEFAULT '',
	`inputname` varchar(255) NOT NULL DEFAULT '',
	`inputtype` varchar(255) NOT NULL DEFAULT '',
	`validation_type` varchar(255) NOT NULL,
	`selectoption` text NOT NULL,
	`setorder` int(11) NOT NULL,
	`reuiredfield` smallint(6) NOT NULL DEFAULT '0',
	`allowed_extensions` varchar(255) NOT NULL,
	`status` smallint(6) NOT NULL DEFAULT '0',
	`is_in_saif` int(11) NOT NULL,
	`is_in_semail` int(11) NOT NULL,
	`store` varchar(255) NOT NULL DEFAULT '',
	`dependable_labelname` varchar(255) NOT NULL,
	`dependable_inputname` varchar(255) NOT NULL,
	`dependable_validation_type` varchar(255) NOT NULL,
	`dependable_inputtype` varchar(255) NOT NULL,
	`dependable_selectoption` text NOT NULL,
	`dependable_reuiredfield` smallint(6) NOT NULL,
	`dependable_allowed_extensions` varchar(255) NOT NULL,
	`created_time` datetime DEFAULT NULL,
	`update_time` datetime DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS {$this->getTable('wk_customfield_data')} (
	`id` int(11) unsigned NOT NULL auto_increment,
	`field_id` int(11) NOT NULL DEFAULT '0',
	`value` text NOT NULL,
	`dependant_value` text NOT NULL,
	`customer_id` int(11) NOT NULL DEFAULT '0',
	`store` varchar(255) NOT NULL DEFAULT '',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
");
$installer->endSetup();
