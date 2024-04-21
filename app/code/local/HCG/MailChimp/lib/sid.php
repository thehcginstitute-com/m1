<?php
use Ebizmarts_MailChimp_Model_Config as Cfg;
/**
 * 2024-04-22 "Refactor `Ebizmarts_MailChimp_Helper_Data::getMCStoreId()`":
 * https://github.com/thehcginstitute-com/m1/issues/574
 * It returns a string like «www.thehcginstitute.com_2017-03-05-013122».
 */
function hcg_mc_sid(int $mgStore):string {return Mage::getStoreConfig(Cfg::GENERAL_MCSTOREID, $mgStore);}