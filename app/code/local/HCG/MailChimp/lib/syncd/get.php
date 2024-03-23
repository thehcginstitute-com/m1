<?php
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as D;
use Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Collection as C;
/**
 * 2024-03-23 "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_CartController::loadcouponAction()
 * @used-by Ebizmarts_MailChimp_CartController::loadquoteAction()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::getDataProduct()
 * @used-by Ebizmarts_MailChimp_Model_Api_Carts::_processCartLines()
 * @used-by Ebizmarts_MailChimp_Model_Api_Customers::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::_getPayloadDataLines()
 * @used-by Ebizmarts_MailChimp_Model_Api_Orders::getSyncedOrder()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::_buildUpdateProductRequest()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::createBatchJson()
 * @used-by Ebizmarts_MailChimp_Model_Api_Products::sendModifiedProduct()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::_getNewPromoCodes()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoCodes::deletePromoCodeSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Api_PromoRules::deletePromoRuleSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Ecommercesyncdata::saveEcommerceSyncData()
 * @used-by Ebizmarts_MailChimp_Model_Observer::cancelCreditMemo()
 * @used-by Ebizmarts_MailChimp_Model_Observer::itemCancel()
 * @used-by Ebizmarts_MailChimp_Model_Observer::newCreditMemo()
 * @used-by Ebizmarts_MailChimp_Model_Observer::newOrder()
 * @used-by Ebizmarts_MailChimp_Model_Observer::productAttributeUpdate()
 * @used-by Ebizmarts_MailChimp_Model_Observer::productSaveAfter()
 */
function hcg_mc_syncd_get(int $id, string $t, string $sid):D {
	$c = new C;
	$c
		# 2024-03-23 `mailchimp_store_id`: «www.thehcginstitute.com_2017-03-05-013122»
		->addFieldToFilter('mailchimp_store_id', ['eq' => $sid])
		->addFieldToFilter('related_id', ['eq' => $id])
		/**
		 * 2024-03-23
		 * `type`:
		 * 		«CUS»: @see Ebizmarts_MailChimp_Model_Config::IS_CUSTOMER
		 * 		«ORD»: @see Ebizmarts_MailChimp_Model_Config::IS_ORDER
		 * 		«PCD»: @see Ebizmarts_MailChimp_Model_Config::IS_PROMO_CODE
		 * 		«PRL»: @see Ebizmarts_MailChimp_Model_Config::IS_PROMO_RULE
		 * 		«PRO»: @see Ebizmarts_MailChimp_Model_Config::IS_PRODUCT
		 * 		«QUO»: @see Ebizmarts_MailChimp_Model_Config::IS_QUOTE
		 * 		«SUB»: @see Ebizmarts_MailChimp_Model_Config::IS_SUBSCRIBER
		 */
		->addFieldToFilter('type', ['eq' => $t])
		->setCurPage(1)
		->setPageSize(1)
	;
	return $c->getSize()
		? $c->getLastItem()
		: new D(['mailchimp_store_id' => $sid, 'related_id' => $id, 'type' => $t])
	;
}