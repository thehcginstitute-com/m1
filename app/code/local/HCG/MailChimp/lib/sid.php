<?php
use Ebizmarts_MailChimp_Model_Config as Cfg;
/**
 * 2024-04-22
 * 1) "Refactor `Ebizmarts_MailChimp_Helper_Data::getMCStoreId()`": https://github.com/thehcginstitute-com/m1/issues/574
 * 2) It returns a string like «www.thehcginstitute.com_2017-03-05-013122».
 * 3) https://3v4l.org/lEukD
 * @used-by Ebizmarts_MailChimp_Block_Adminhtml_Sales_Order_Grid_Renderer_MailchimpOrder::render()
 * @used-by Ebizmarts_MailChimp_CartController::loadquoteAction()
 * @used-by Ebizmarts_MailChimp_CartController::loadcouponAction()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getStoreRelation()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMagentoStoresForMCStoreIdByScope()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getDateSyncFinishByStoreId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::resendMCEcommerceData()
 * @used-by Ebizmarts_MailChimp_Helper_Data::removeEcommerceSyncData()
 * @used-by Ebizmarts_MailChimp_Helper_Data::isEcomSyncDataEnabled()
 * @used-by Ebizmarts_MailChimp_Helper_Data::clearErrorGrid()
 * @used-by Ebizmarts_MailChimp_Helper_Data::handleOldErrors()
 * @used-by Ebizmarts_MailChimp_Helper_Data::saveLastItemsSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastCustomerSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastProductSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastOrderSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastCartSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getLastPromoCodeSent()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMailChimpScopeByStoreId()
 * @used-by Ebizmarts_MailChimp_Helper_Data::getMCJs()
 * @used-by Ebizmarts_MailChimp_Helper_Data::retrieveAndSaveMCJsUrlInConfig()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 */
function hcg_mc_sid(int $mgStore = null):?string {return Mage::getStoreConfig(Cfg::GENERAL_MCSTOREID, $mgStore);}