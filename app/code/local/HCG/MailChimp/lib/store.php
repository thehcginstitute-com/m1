<?php
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Mage_Core_Model_Store as S;
/**
 * 2024-06-03 $mcListId is a string like «147da8cfd6».
 * @used-by hcg_mc_sub()
 * @used-by Ebizmarts_MailChimp_WebhookController::indexAction()
 * @return int[]
 */
function hcg_mc_store_id(string $mcListId):?int {return dfcf(function(string $mcListId):?int {/** @var S $s */ return
	($s = df_find(
		function(S $s) use($mcListId):bool {return
			hcg_mc_h()->isSubscriptionEnabled($s->getId()) && $mcListId === $s->getConfig(Cfg::GENERAL_LIST)
		;}, Mage::app()->getStores()
	)) ?  $s->getId() : null
;}, [$mcListId]);}