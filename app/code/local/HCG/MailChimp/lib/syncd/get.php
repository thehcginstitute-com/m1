<?php
use Ebizmarts_MailChimp_Model_Ecommercesyncdata as D;
use Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Collection as C;
/**
 * 2024-03-23 "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
 * @used-by Ebizmarts_MailChimp_CartController::loadquoteAction()
 * @used-by Ebizmarts_MailChimp_CartController::loadcouponAction()
 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::getDataProduct()
 * @used-by Ebizmarts_MailChimp_Model_Api_Carts::_processCartLines()
 * @param $id
 * @param $type
 * @param $sid
 */
function hcg_mc_syncd_get($id, $type, $sid):D {
	$c = new C;
	$c
		->addFieldToFilter('mailchimp_store_id', ['eq' => $sid])
		->addFieldToFilter('related_id', ['eq' => $id])
		->addFieldToFilter('type', ['eq' => $type])
		->setCurPage(1)
		->setPageSize(1)
	;
	return $c->getSize()
		? $c->getLastItem()
		: hcg_mc_syncd_new()->addData(['mailchimp_store_id' => $sid, 'related_id' => $id, 'type' => $type])
	;
}