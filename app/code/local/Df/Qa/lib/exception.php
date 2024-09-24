<?php
use Df\Core\Exception as DFE;
use Exception as X;
use Throwable as T; # 2023-08-02 "Treat `\Throwable` similar to `\Exception`": https://github.com/mage2pro/core/issues/311

/**
 * 2023-08-02
 * 2024-01-10 "Port `df_is_th` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/170
 * @see df_is_x()
 * @used-by df_bt()
 * @used-by df_bt_inc()
 * @used-by df_error_create()
 * @used-by df_log()
 * @used-by df_log_l()
 * @used-by df_sentry()
 * @used-by df_try()
 * @used-by df_xts()
 * @used-by df_xtsd()
 * @used-by Df\Core\Exception::__construct()
 */
function df_is_th($v):bool {return $v instanceof T;}

/**
 * 2023-08-02
 * 2024-02-03 "Port `df_is_x()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/347
 * @see df_is_th()
 * @used-by df_lxts()
 * @used-by df_th2x()
 */
function df_is_x($v):bool {return $v instanceof X;}

/**
 * 2023-08-03
 * 2024-02-03 "Port `df_xf()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/344
 * @used-by df_lx()
 * @used-by Df\Core\Exception::__construct()
 */
function df_th2x(T $t):X {return df_is_x($t) ? $t : new X(df_xts($t), $t->getCode(), $t);}

/**
 * 2016-07-18
 * 2024-02-03 "Port `df_xf()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/343
 * @used-by Df\Qa\Failure\Exception::trace()
 */
function df_xf(T $t):T {while ($t->getPrevious()) {$t = $t->getPrevious();} return $t;}

/**
 * @see \Df\Core\Exception::throw_()
 * @used-by df_lx()
 * @used-by df_lxts()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_xml_x()
 * @used-by Df\Qa\Failure\Error::check()
 * @used-by Df\Qa\Failure\Error::log()
 * @used-by Df\Qa\Trace\Formatter::frame()
 * @used-by Df\Xml\G::addChild()
 * @used-by Df\Xml\G::importString()
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::createNewWebhook() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::shouldSendCampaignId() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::getPromoCodesForRule() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::deleteMailChimpStore() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::getUserFriendlyMessage() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_catchMailchimpException() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_catchMailchimpSubsNotAppliedElse() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::_catchMailchimpSubsNotAppliedIf() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers::deleteSubscriber() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::_getSubscribedGroups() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by HCG\MailChimp\Batch\GetBatchResponse::p() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Mage_Adminhtml_CustomerController::validateAction()
 * @param X|string $e
 */
function df_xts($t):string {return df_path_rel_g(
	!df_is_th($t) ? $t : ($t instanceof DFE ? $t->message() : $t->getMessage())
);}