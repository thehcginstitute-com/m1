<?php

$installer = $this;

try {
    $installer->run(
        "
 ALTER TABLE `{$this->getTable('mailchimp_ecommerce_sync_data')}`
 ADD column `batch_id` VARCHAR (10) DEFAULT NULL;
"
    );

    hcg_mc_cfg_save(
            array(
                array(
                    Ebizmarts_MailChimp_Model_Config::GENERAL_MIGRATE_FROM_1164,
                    1)
            ),
            0,
            'default'
        );
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'MailChimp_Errors.log', true);
}

$installer->endSetup();
