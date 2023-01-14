<?php
$installer = $this;
$installer->startSetup();
$installer->run("


		DROP TABLE IF EXISTS  {$this->getTable('iwd_opc_signature')};

		CREATE TABLE `{$this->getTable('iwd_opc_signature')}` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`order_id` int(11) NOT NULL,
		`signature_name` text CHARACTER SET utf8,
		`signature_json` text NOT NULL,
		 PRIMARY KEY (`id`)
		) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
		");

$installer->endSetup();
