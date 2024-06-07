<?php
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
 * @used-by \Df\Core\Exception::__construct()
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
 * Эта функция используется, как правило, при отключенном режиме разработчика.
 * @see mageCoreErrorHandler():
		if (Mage::getIsDeveloperMode()) {
			throw new Exception($errorMessage);
		}
 		else {
			Mage::log($errorMessage, Zend_Log::ERR);
		}
 * @param bool $isOperationSuccessfull [optional]
 */
function df_throw_last_error($isOperationSuccessfull = false) {
	if (!$isOperationSuccessfull) {
		\Df\Qa\Failure\Error::throwLast();
	}
}

/**
 * 2023-08-03
 * 2024-02-03 "Port `df_xf()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/344
 * @used-by df_lx()
 * @used-by \Df\Core\Exception::__construct()
 */
function df_th2x(T $t):X {return df_is_x($t) ? $t : new X(df_xts($t), $t->getCode(), $t);}

/**
 * 2016-07-18
 * 2024-02-03 "Port `df_xf()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/343
 * @used-by \Df\Qa\Failure\Exception::trace()
 */
function df_xf(T $t):T {while ($t->getPrevious()) {$t = $t->getPrevious();} return $t;}

/**
 * @see \Df\Core\Exception::throw_()
 * @used-by df_lx()
 * @used-by df_lxts()
 * @used-by df_message_error()
 * @used-by df_sprintf_strict()
 * @used-by df_xml_parse()
 * @used-by Mage_Adminhtml_CustomerController::validateAction()
 * @used-by \Df\Qa\Failure\Error::check()
 * @used-by \Df\Qa\Failure\Error::log()
 * @used-by \Df\Qa\Trace\Formatter::frame()
 * @used-by Ebizmarts_MailChimp_Helper_Webhook::createNewWebhook() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Subscribers_InterestGroupHandle::_getSubscribedGroups() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::shouldSendCampaignId() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::getPromoCodesForRule() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by Ebizmarts_MailChimp_Model_Api_Stores::getUserFriendlyMessage() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by STUB() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by STUB() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by STUB() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by STUB() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @used-by STUB() (https://github.com/thehcginstitute-com/m1/issues/524)
 * @param X|string $e
 * @return string
 */
function df_xts($e) {return df_adjust_paths_in_message(!$e instanceof X ? $e : $e->getMessage());}