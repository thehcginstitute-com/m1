<?php
$installer = $this;
$installer->startSetup();
# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused `visitorid` field from `Pma_Importer`": https://github.com/thehcginstitute-com/m1/issues/417
$sql = <<<SQLTEXT
CREATE TABLE IF NOT EXISTS {$this->getTable('importer_setting')} (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `webservice_url` text NOT NULL,
  `account_token` text NOT NULL,
  `updated_on` datetime NOT NULL,
  `orders_type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
SQLTEXT;
$installer->run($sql);
$installer->endSetup();