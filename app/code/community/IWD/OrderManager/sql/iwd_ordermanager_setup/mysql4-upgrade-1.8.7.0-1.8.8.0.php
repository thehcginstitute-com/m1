<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

try {
    $status = Mage::getModel('sales/order_status');
    $status->setStatus('iwd_om_outofstock');
    $status->setLabel('Order Backordered');
    $status->assignState('new');
    $status->save();
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_ordermanager_install.log');
}

$installer->endSetup();
