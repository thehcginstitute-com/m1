<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Model_Api_Batches as Sb;
use Ebizmarts_MailChimp_Model_Config as Cfg;
# 2024-04-22 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class Subscriber {
	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by Ebizmarts_MailChimp_Model_Cron::syncSubscriberBatchData()
	 */
	static function p():void {
		$sb = hcg_mc_batches_new(); /** @var Sb $sb */
		$limit = (int)\Mage::getStoreConfig(Cfg::GENERAL_SUBSCRIBER_AMOUNT, 0); /** @var int $limit */
		# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# https://3v4l.org/AF1Vc
		foreach (\Mage::app()->getStores() as $s) {
			$sid = (int)$s->getId(); /** @var int $sid */
			GetResults::p($sid, false);
			if (1 > $limit) {
				break;
			}
			$limit = $sb->sendStoreSubscriberBatch($sid, $limit);
		}
		GetResults::p(0, false);
		if (0 < $limit) {
			$sb->sendStoreSubscriberBatch(0, $limit);
		}
	}
}