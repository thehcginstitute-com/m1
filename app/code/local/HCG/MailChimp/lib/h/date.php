<?php
use Ebizmarts_MailChimp_Helper_Date as H;
/**
 * 2024-04-21
 * @used-by Ebizmarts_MailChimp_GroupController::getCurrentDateTime()
 * @used-by Ebizmarts_MailChimp_Helper_Data::saveInterestGroupData()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_updateSyncingFlag()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::markItemsAsSent()
 * @used-by Ebizmarts_MailChimp_Model_Api_Carts::_getNewQuotes()
 * @used-by Ebizmarts_MailChimp_Model_Api_Carts::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::makeBatchId()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::_getModifiedOrders()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::_buildNewProductRequest()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::_buildUpdateProductRequest()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::_markSpecialPrices()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::makeBatchId()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::sendModifiedProduct()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_getNewPromoCodes()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::setCodeWithParentError()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::getNewPromoRule()
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::createMailChimpStore()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::updateSubscriber()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::processGroupsData()
 * @used-by \HCG\MailChimp\Tags::getDateOfBirth()
 * @used-by Ebizmarts_MailChimp_Model_ClearEcommerce::getPromoCodeItems()
 * @used-by Ebizmarts_MailChimp_Model_Email_Queue::_saveMessage()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_List::_afterSave()
 */
function hcg_mc_h_date():H {return Mage::helper('mailchimp/date');}