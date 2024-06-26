<?php
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as D;
/**
 * 2024-03-22 "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastCartSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastCustomerSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastOrderSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastProductSent()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::saveSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::_updateSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::markAllSyncDataAsModified()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_setDeleted()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_setModified()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::deletePromoCodesSyncDataByRule()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::getPromoCodesForRule()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::makeDeletedPromoCodesCollection()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::_setDeleted()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::_setModified()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::makeModifiedAndDeletedPromoRulesCollection()
 */
function hcg_mc_syncd_new():D {return new D;}