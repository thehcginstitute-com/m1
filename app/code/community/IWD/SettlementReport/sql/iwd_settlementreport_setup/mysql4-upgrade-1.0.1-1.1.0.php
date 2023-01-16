<?php
$installer = $this;
$installer->startSetup();

$paymentTransaction = $this->getTable('sales/payment_transaction');

try {
    $installer->run(
        "ALTER TABLE {$paymentTransaction} ADD COLUMN `amount` DECIMAL (12,4) NULL;"
    );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_settlement_report_install.log');
}

$installer->endSetup();
