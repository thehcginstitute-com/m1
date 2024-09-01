<?php
use Df\Qa\Failure\Exception as QE;
use Exception as E;
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
 * @param _DO|mixed[]|mixed|E $v
 * @param string|object|null $m [optional]
 */
function df_log($v, $m = null, array $d = [], string $suf = ''):void {
	$isT = df_is_th($v); /** @var bool $isT */
	$m = $m ? df_module_name($m) : ($isT ? df_caller_module($v) : df_caller_module());
	df_log_l($m, ...($isT ? [$v, $d] : [!$d ? $v : (dfa_merge_r($d, is_array($v) ? $v : ['message' => $v])), $suf]));
}

/**
 * 2017-01-11
 * 2024-01-10 "Port the latest version of `df_log_l` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/169
 * @used-by df_log()
 * @used-by df_log_e()
 * @used-by dfp_report()
 * @used-by Df_Core_Model_Layout::_getBlockInstance()
 * @param string|object|null $m
 * @param string|mixed[]|E $p2
 * @param string|mixed[]|E $p3 [optional]
 * @param string|bool|null $p4 [optional]
 */
function df_log_l($m, $p2, $p3 = [], string $p4 = ''):void {
	/** @var T|null $t */ /** @var array|string|mixed $d */ /** @var string $suf */ /** @var string $pref */
	# 2024-06-06 "Use the «Symmetric array destructuring» PHP 7.1 feature": https://github.com/mage2pro/core/issues/379
	[$t, $d, $suf, $pref] = df_is_th($p2) ? [$p2, $p3, $p4, ''] : [null, $p2, df_ets($p3), $p4];
	$m = $m ?: ($t ? df_caller_module($t) : df_caller_module());
	if (!$suf) {
		# 2023-07-26
		# 1) "If `df_log_l()` is called from a `*.phtml`,
		# then the `*.phtml`'s base name  should be used as the log file name suffix instead of `df_log_l`":
		# https://github.com/mage2pro/core/issues/269
		# 2) 2023-07-26 "Add the `$skip` optional parameter to `df_caller_entry()`": https://github.com/mage2pro/core/issues/281
		$entry = $t ? df_caller_entry_m($t) : df_caller_entry(0, null, ['df_log']); /** @var array(string => string|int) $entry */
		$suf = df_bt_entry_is_phtml($entry) ? basename(df_bt_entry_file($entry)) : df_bt_entry_func($entry);
	}
	$c = df_context(); /** @var array(string => mixed) $c */
	df_report(
		df_ccc('--', 'mage2.pro/' . df_ccc('-', df_report_prefix($m, $pref), '{date}--{time}'), $suf) .  '.log'
		# 2023-07-26
		# "`df_log_l()` should use the exception's trace instead of `df_bt_s(1)` for exceptions":
		# https://github.com/mage2pro/core/issues/261
		,df_cc_n(
			# 2023-07-28
			# "`df_log_l` does not log the context if the message is not an array":
			# https://github.com/mage2pro/core/issues/289
			/** @uses df_dump_ds() */
			df_map('df_dump_ds', !$d ? [$c] : (is_array($d) ? [dfa_merge_r($d, ['Mage2.PRO' => $c])] : [$d, $c]))
			,!$t ? '' : ['EXCEPTION', QE::i($t)->report(), "\n\n"]
			,($t ? null : "\n") . df_bt_s($t ?: 1)
		)
	);
}

/**
 * 2017-04-03
 * 2017-04-22
 * С не-строковым значением $m @uses \Magento\Framework\Filesystem\Driver\File::fileWrite() упадёт,
 * потому что там стоит код: $lenData = strlen($data);
 * 2018-07-06 The `$append` parameter has been added.
 * 2020-02-14 If $append is `true`, then $m will be written on a new line.
 * @used-by df_bt_log()
 * @used-by df_log_l()
 * @used-by \Df\Qa\Failure\Error::log()
 * @param string $f
 * @param string $m
 * @param bool $append [optional]
 */
function df_report($f, $m, $append = false) {
	if (!df_es($m)) {
		$f = df_file_ext_def($f, 'log');
		$p = BP . '/var/log'; /** @var string $p */
		df_file_write($append ? "$p/$f" : df_file_name($p, $f), $m, $append);
	}
}

/**
 * 2020-01-31
 * 2023-07-19
 * «mb_strtolower(): Passing null to parameter #1 ($string) of type string is deprecated
 * in vendor/mage2pro/core/Qa/lib/log.php on line 122»: https://github.com/mage2pro/core/issues/233
 * 2024-01-11 "Port `df_report_prefix` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/177
 * @used-by df_log_l()
 * @param string|object|null $m [optional]
 */
function df_report_prefix($m = null, string $pref = ''):string {return df_ccc('--',
	mb_strtolower($pref), !$m ? null : df_module_name_lc($m, '-')
);}