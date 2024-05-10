<?php
use Mage_Core_Model_Store as S;
/**
 * 2024-05-10 "Port `df_cfg()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/592
 * @used-by hcg_mc_cfg_fields()
 * @used-by hcg_mc_sid()
 * @used-by HCG\MagePsycho\Helper::cfg()
 * @used-by HCG\MailChimp\Batch\Subscriber::p()
 * @used-by HCG_Payment_BankCard::prepareSave()
 * @used-by HCG_WP_Settings::url()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_canCancel()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_popupHeading()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_popupMessage()
 * @used-by Ebizmarts_MailChimp_Block_Popup_Emailcatcher::_modalSubscribe()
 * @used-by Ebizmarts_MailChimp_CartController::loadquoteAction()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getConfigValueForScope()
 * @used-by Ebizmarts_MailChimp_Model_Email_Template::getSendingSetReturnPath()
 * @used-by Ebizmarts_MailChimp_Model_Email_Template::getSendingReturnPathEmail()
 * @used-by Ebizmarts_MailChimp_Model_Email_Template::getGeneralEmail()
 * @used-by Ebizmarts_MailChimp_Model_Email_Template::isMandrillEnabled()
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
 * @used-by STUB()
 * @param null|string|bool|int|S $s
 * @return mixed
 */
function df_cfg(string $k, $s = null) {return Mage::getStoreConfig($k, $s);}