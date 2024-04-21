<?php
use Ebizmarts_MailChimp_Helper_Data as H;
/**
 * 2024-04-14
 * @used-by hcg_mc_cfg_scope()
 * @used-by Ebizmarts_MailChimp_Adminhtml_EcommerceController::makeHelper()
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimpController::preDispatch()
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::makeHelper()
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimpstoresController::getMailchimpHelper()
 * @used-by Ebizmarts_MailChimp_Adminhtml_MergevarsController::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Customer_Edit_Tab_Mailchimp::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Mailchimpstores_Edit::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Mailchimpstores_Edit_Form::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Notifications::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_View_Info_Monkey::getMailChimpHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_System_Config_Account::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_System_Config_CreateMergeFields::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_System_Config_Fieldset_Mailchimp_Hint::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_System_Config_Form_Field_Mapfields::makeHelper()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Subscribe::__construct()
 * @used-by Ebizmarts_MailChimp_Block_Checkout_Success_Groups::__construct()
 * @used-by Ebizmarts_MailChimp_Block_Customer_Newsletter_Index::__construct()
 * @used-by Ebizmarts_MailChimp_Block_Group_Type::__construct()
 * @used-by Ebizmarts_MailChimp_CartController::loadcouponAction::loadquoteAction()
 * @used-by Ebizmarts_MailChimp_Helper_Mandrill::getMandrillApiKey()
 * @used-by Ebizmarts_MailChimp_Helper_Mandrill::isMandrillEnabled()
 * @used-by Ebizmarts_MailChimp_Helper_Mandrill::isMandrillLogEnabled()
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::__construct()
 * @used-by Ebizmarts_MailChimp_Model_Adminhtml_Includetaxes_Comment::getCommentText()
 * @used-by Ebizmarts_MailChimp_Model_Adminhtml_Resendecommercedata_Comment::setMcHelper()
 * @used-by Ebizmarts_MailChimp_Model_Adminhtml_Resendsubscribers_Comment::setMcHelper()
 * @used-by Ebizmarts_MailChimp_Model_Adminhtml_Reseterrors_Comment::setMcHelper()
 * @used-by Ebizmarts_MailChimp_Model_Adminhtml_Storeid_Comment::getCommentText()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_getResults()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_processBatchOperations()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_saveItemStatus()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_sendEcommerceBatch()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_showResumeDataSentToMailchimp()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_showResumeEcommerce()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_showResumeSubscriber()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::_updateSyncingFlag()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::addSyncValueToArray()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::deleteBatchItems()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::deleteUnsentItems()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::handleEcommerceBatches()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::handleErrorItem()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::handleSubscriberBatches()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::handleSyncingValue()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::markItemsAsSent()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::processEachResponseFile()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::saveSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::sendStoreSubscriberBatch()
 * @used-by Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::__construct()
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::__construct()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::__construct()
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_MailchimpTags::setMailChimpHelper()
 * @used-by Ebizmarts_MailChimp_Model_ClearBatches::__construct()
 * @used-by Ebizmarts_MailChimp_Model_ClearEcommerce::__construct()
 * @used-by Ebizmarts_MailChimp_Model_Cron::__construct()
 * @used-by Ebizmarts_MailChimp_Model_Observer::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Active::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Apikey::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Ecommerce::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_List::getMailchimpHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Mapfield::_afterLoad()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Store::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Twowaysync::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_Account::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_CustomerGroup::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_List::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_Store::makeHelper()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_Userinfo::toOptionArray()
 * @used-by HCG\MailChimp\Batch\HandleErrorItem::p()
 * @used-by app/design/adminhtml/default/default/template/ebizmarts/mailchimp/system/config/fieldset/hint.phtml
 * @used-by app/design/adminhtml/default/default/template/ebizmarts/mandrill/system/config/fieldset/hint.phtml
 */
function hcg_mc_h():H {return Mage::helper('mailchimp');}