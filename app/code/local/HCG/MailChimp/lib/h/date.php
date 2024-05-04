<?php
use Ebizmarts_MailChimp_Helper_Date as H;
/**
 * 2024-04-21
 * @used-by Ebizmarts_MailChimp_GroupController::getCurrentDateTime()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_updateSyncingFlag()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::markItemsAsSent()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::updateSubscriber()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::getDateOfBirth()
 * @used-by Ebizmarts_MailChimp_Model_Email_Queue::_saveMessage()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_List::_afterSave()
 * @used-by Ebizmarts_MailChimp_Helper_Data::saveInterestGroupData()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::processGroupsData()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 */
function hcg_mc_h_date():H {return Mage::helper('mailchimp/date');}