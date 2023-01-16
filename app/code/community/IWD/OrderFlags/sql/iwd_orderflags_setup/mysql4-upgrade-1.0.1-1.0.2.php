<?php

/** @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

try {
    $installer->run(
        "CREATE INDEX iwd_flag_order_id_index ON {$this->getTable('iwd_om_flags_orders')} (order_id);
        CREATE INDEX iwd_flag_flag_id_index ON {$this->getTable('iwd_om_flags_orders')} (flag_id);
        CREATE INDEX iwd_flag_type_id_index ON {$this->getTable('iwd_om_flags_orders')} (type_id);
        CREATE INDEX iwd_flag_type_id_index ON {$this->getTable('iwd_om_flags_flag_type')} (type_id);
        CREATE INDEX iwd_flag_flag_id_index ON {$this->getTable('iwd_om_flags_flag_type')} (flag_id);
        CREATE INDEX iwd_flag_type_id_index ON {$this->getTable('iwd_om_flags_autoapply')} (type_id);"
    );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'iwd_ordermanager_install.log');
}

$installer->endSetup();
