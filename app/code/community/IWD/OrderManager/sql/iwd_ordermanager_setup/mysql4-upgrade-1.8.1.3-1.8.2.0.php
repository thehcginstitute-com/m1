<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

try {
    $installer->run(
        "ALTER TABLE {$this->getTable('iwd_backup_flat_sales')} ADD COLUMN `entity_id` int (10) DEFAULT NULL;
        ALTER TABLE {$this->getTable('iwd_backup_flat_sales')} ADD COLUMN `after_action` VARCHAR (10) DEFAULT 'delete';"
    );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_ordermanager_install.log');
}

try {
    $installer->run(
        "ALTER TABLE {$this->getTable('sales/order')} ADD COLUMN `iwd_backup_id` int (10) NULL;"
    );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_ordermanager_install.log');
}

$installer->endSetup();
