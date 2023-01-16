<?php
$installer = $this;
$installer->startSetup();

try {
    $installer->run(
        "CREATE TABLE IF NOT EXISTS {$this->getTable('iwd_auth_payment_transaction')}(
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `payment_transaction_id` int(10) NULL COMMENT 'Magento Payment Transaction Id',
    
            `transaction_id` varchar(100) DEFAULT NULL COMMENT 'Transaction Id',
            `transaction_type` varchar(100) DEFAULT NULL COMMENT 'Transaction Type',
    
            `auth_transaction_status` varchar(100) DEFAULT NULL COMMENT 'Authorize Transaction Status',
            `mage_transaction_status` varchar(100) DEFAULT NULL COMMENT 'Magento Transaction Status',
    
            `auth_amount_authorized` decimal(12,4) DEFAULT NULL COMMENT 'Amount Authorized',
            `auth_amount_captured` decimal(12,4) DEFAULT NULL COMMENT 'Amount Captured',
            `auth_amount_settlement` decimal(12,4) DEFAULT NULL COMMENT 'Amount Settlement',
            `auth_amount_refund` decimal(12,4) DEFAULT NULL COMMENT 'Amount Refund',
    
            `mage_amount_authorized` decimal(12,4) DEFAULT NULL COMMENT 'Magento Amount Authorized',
            `mage_amount_captured` decimal(12,4) DEFAULT NULL COMMENT 'Magento Amount Captured',
            `mage_amount_settlement` decimal(12,4) DEFAULT NULL COMMENT 'Magento Amount Settlement',
            `mage_amount_refund` decimal(12,4) DEFAULT NULL COMMENT 'Magento Amount Refund',
    
            `order_increment_id`  varchar(100) DEFAULT NULL COMMENT 'Order Increment ID',
            `order_id` int(10) DEFAULT NULL COMMENT 'Order ID',
    
            `status` int(2) NULL COMMENT 'Compare Status',
    
            `created_at` timestamp NULL DEFAULT NULL COMMENT 'Payment Created At',
            PRIMARY KEY (`id`)
        ) ENGINE=INNODB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;"
    );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_settlement_report_install.log');
}

$installer->endSetup();
