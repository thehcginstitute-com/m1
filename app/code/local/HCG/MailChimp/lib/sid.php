<?php
use Ebizmarts_MailChimp_Model_Config as Cfg;
/**
 * 2024-04-22
 * 1) "Refactor `Ebizmarts_MailChimp_Helper_Data::getMCStoreId()`": https://github.com/thehcginstitute-com/m1/issues/574
 * 2) It returns a string like «www.thehcginstitute.com_2017-03-05-013122».
 * 3) https://3v4l.org/lEukD
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder::render()
 * @used-by Ebizmarts_MailChimp_CartController::loadcouponAction()
 * @used-by Ebizmarts_MailChimp_CartController::loadquoteAction()
 * @used-by Ebizmarts_MailChimp_Helper_Data::clearErrorGrid()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getDateSyncFinishByStoreId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastCartSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastCustomerSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastOrderSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastProductSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastPromoCodeSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMCJs()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMagentoStoresForMCStoreIdByScope()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getStoreRelation()
 * @used-by Ebizmarts_MailChimp_Helper_Data::handleOldErrors()
 * @used-by Ebizmarts_MailChimp_Helper_Data::isEcomSyncDataEnabled()
 * @used-by Ebizmarts_MailChimp_Helper_Data::isMissingCustomerLowerThanId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::isMissingOrderLowerThanId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::isMissingProductLowerThanId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::isMissingQuoteLowerThanId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::removeEcommerceSyncData()
 * @used-by Ebizmarts_MailChimp_Helper_Data::resendMCEcommerceData()
 * @used-by Ebizmarts_MailChimp_Helper_Data::retrieveAndSaveMCJsUrlInConfig()
 * @used-by Ebizmarts_MailChimp_Helper_Data::saveLastItemsSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::setIsSyncingIfFinishedPerScope()
 * @used-by Ebizmarts_MailChimp_Model_Observer::cancelCreditMemo()
 * @used-by Ebizmarts_MailChimp_Model_Observer::customerAddressSaveBefore()
 * @used-by Ebizmarts_MailChimp_Model_Observer::customerSaveAfter()
 * @used-by Ebizmarts_MailChimp_Model_Observer::itemCancel()
 * @used-by Ebizmarts_MailChimp_Model_Observer::newCreditMemo()
 * @used-by Ebizmarts_MailChimp_Model_Observer::newOrder()
 * @used-by Ebizmarts_MailChimp_Model_Observer::orderSaveBefore()
 * @used-by Ebizmarts_MailChimp_Model_Observer::productSaveAfter()
 * @used-by Ebizmarts_MailChimp_Model_Observer::saveConfigBefore()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Backend_Store::_afterSave()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_Account::__construct()
 * @used-by Ebizmarts_MailChimp_Model_System_Config_Source_List::__construct()
 * @used-by HCG\MailChimp\Batch\Commerce::addSyncValueToArray()
 * @used-by HCG\MailChimp\Batch\Commerce\Send::p()
 * @used-by HCG\MailChimp\Batch\GetResults::p()
 */
function hcg_mc_sid(int $mgStore = null):?string {return df_cfg(Cfg::GENERAL_MCSTOREID, $mgStore);}