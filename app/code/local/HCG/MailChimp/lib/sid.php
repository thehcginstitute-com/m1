<?php
use Ebizmarts_MailChimp_Model_Config as Cfg;
/**
 * 2024-04-22 "Refactor `Ebizmarts_MailChimp_Helper_Data::getMCStoreId()`":
 * https://github.com/thehcginstitute-com/m1/issues/574
 * It returns a string like «www.thehcginstitute.com_2017-03-05-013122».
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
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 * @used-by STUB()
 */
function hcg_mc_sid(int $mgStore = 0):string {return Mage::getStoreConfig(Cfg::GENERAL_MCSTOREID, $mgStore);}