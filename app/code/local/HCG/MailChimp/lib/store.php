<?php
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Mage_Core_Model_Store as S;
/**
 * 2024-06-03 $mcListId is a string like «147da8cfd6».
 */
function hcg_mc_stores(string $mcListId):array {return dfcf(function(string $mcListId):array {return df_find(
	function(S $s) use($mcListId):bool {return
		hcg_mc_h()->isSubscriptionEnabled($s->getId()) && $mcListId === $s->getConfig(Cfg::GENERAL_LIST)
	;}, Mage::app()->getStores()
);}, [$mcListId]);}