<?php
$installer = $this;
hcg_mc_cfg_save(Ebizmarts_MailChimp_Model_Config::GENERAL_MIGRATE_FROM_116, 1);
$installer->endSetup();