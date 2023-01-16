<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

/** ALTER ORDER HISTORY TABLE **/
try {
    $table = $this->getTable('sales_flat_order_status_history');
    $installer->run(
        "ALTER TABLE {$table} ADD `admin_id` INT NULL DEFAULT NULL;
        ALTER TABLE {$table} ADD `admin_email` VARCHAR(100) NULL DEFAULT NULL;"
    );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_ordermanager_install.log');
}

$installer->endSetup();
