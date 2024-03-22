<?php
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as SyncD;
/**
 * 2024-03-22 "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_Model_Api_Carts::_processCartLines()
 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::_updateSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::markAllSyncDataAsModified()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::_getPayloadDataLines()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::getSyncedOrder()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::_buildUpdateProductRequest()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::sendModifiedProduct()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_getNewPromoCodes()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_setDeleted()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_setModified()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::deletePromoCodeSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::deletePromoCodesSyncDataByRule()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::getPromoCodesForRule()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::makeDeletedPromoCodesCollection()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::_setDeleted()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::_setModified()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::deletePromoRuleSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::makeModifiedAndDeletedPromoRulesCollection()
 * @used-by Ebizmarts_MailChimp_Model_Observer::productAttributeUpdate()
 * @used-by Ebizmarts_MailChimp_Model_Observer::productSaveAfter()
 * @used-by Ebizmarts_MailChimp_Model_Observer::itemCancel()
 * @used-by Ebizmarts_MailChimp_Model_Observer::cancelCreditMemo()
 * @used-by Ebizmarts_MailChimp_Model_Observer::newCreditMemo()
 */
function hcg_mc_syncd_new():SyncD {return new SyncD;}