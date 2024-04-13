<?php

$installer = $this;

try {
    $webhookData = array();

    /* Check if webhook is created */
    $configDataCollection = Mage::getModel('core/config_data')
        ->getCollection()
        ->addFieldToFilter('path', 'mailchimp/general/webhook_id');

    /* If webhook is created, edites it and place the new "event" variable */
    if ($configDataCollection->getSize()) {
        hcg_mc_cfg_save(Ebizmarts_MailChimp_Model_Config::GENERAL_MIGRATE_FROM_1120, 1);
    }
} catch (Exception $e) {
    Mage::log($e->getMessage(), null, 'MailChimp_Errors.log', true);
}

$installer->endSetup();
