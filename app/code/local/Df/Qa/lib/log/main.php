<?php
use Throwable as T; # 2023-08-03 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311
use Varien_Object as _DO;

/**
 * 2024-01-10 "Port the latest version of `df_log` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/168
 * @used-by mageCoreErrorHandler()
 * @used-by mageFindClassFile() (https://github.com/thehcginstitute-com/m1/issues/556)
 * @used-by Aoe_Scheduler_Model_Observer::dispatch()
 * @used-by Df\Qa\Failure\Error::check()
 * @used-by Df\Qa\Failure\Error::log()
 * @used-by Df_Core_Model_Layout::_getBlockInstance()
 * @used-by Ebizmarts_MailChimp::call()
 * @used-by Ebizmarts_MailChimp_Adminhtml_EcommerceController::resetLocalErrorsAction() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimpstoresController::_loadStores() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimpstoresController::getstoresAction() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::_addMergeFieldByLabel() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::_createCustomFieldTypes() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::addResendFilter() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::createMergeFields() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getApi() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getApiByMailChimpStoreId() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getInterest() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getListIdByApiKeyAndMCStoreId() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getListInterestCategoriesByKeyAndList() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getListInterestGroups() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMailChimpCampaignNameById() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::isNewApiKeyForSameAccount() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::removeAllEcommerceSyncDataErrors() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::removeEcommerceSyncDataByMCStore() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::retrieveAndSaveMCJsUrlInConfig() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Data::setIsSyncingIfFinishedPerScope() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::createNewWebhook() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::deleteCurrentWebhook() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::deleteCurrentWebhook() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_ItemSynchronizer::logSyncError()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_getNewPromoCodes() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::createMailChimpStore() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::deleteMailChimpStore() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::editMailChimpStore() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_catchMailchimpException() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_catchMailchimpSubsNotAppliedElse() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_catchMailchimpSubsNotAppliedIf() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::createBatchJson() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::deleteSubscriber() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::updateSubscriber() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::_getSubscribedGroups() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::processGroupsData() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Observer::newOrder() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_Account::__construct() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_List::__construct() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_Store::__construct() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_WebhookController::_deleteWebhook() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Batch\Commerce::handleSyncingValue() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Batch\Commerce::p() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Batch\Commerce\Send::p() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Batch\GetBatchResponse::p() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Batch\HandleErrorItem::p() (https://github.com/thehcginstitute-com/m1/issues/565)
 * @used-by HCG\MailChimp\Batch\Subscriber::sendStoreSubscriberBatch()
 * @used-by HCG\MailChimp\Batch\Subscriber::sendStoreSubscriberBatch() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Tags::_p()
 * @used-by HCG\MailChimp\Tags\ProcessMergeFields::_addSubscriberData() (https://github.com/thehcginstitute-com/m1/issues/589)
 * @used-by Mage::logException()
 * @used-by Mage::printException()
 * @used-by Mage_Adminhtml_CustomerController::validateAction()
 * @used-by Varien_Data_Collection_Db::addItem()
 * @param _DO|mixed[]|mixed|T $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null, array $d = [], string $suf = ''):void {
	$isT = df_is_th($v); /** @var bool $isT */
	$m = $m ? df_module_name($m) : ($isT ? df_caller_module($v) : df_caller_module());
	df_log_l($m, ...($isT ? [$v, $d] : [!$d ? $v : (dfa_merge_r($d, is_array($v) ? $v : ['message' => $v])), $suf]));
}