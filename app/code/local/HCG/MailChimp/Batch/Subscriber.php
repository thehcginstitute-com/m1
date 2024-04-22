<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Helper_Data_ApiKeyException as EApiKey;
use Ebizmarts_MailChimp_Model_Api_Subscribers as Api;
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
# 2024-04-22 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class Subscriber {
	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by Ebizmarts_MailChimp_Model_Cron::syncSubscriberBatchData()
	 */
	static function p():void {
		$limit = (int)\Mage::getStoreConfig(Cfg::GENERAL_SUBSCRIBER_AMOUNT, 0); /** @var int $limit */
		# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# https://3v4l.org/AF1Vc
		foreach (\Mage::app()->getStores() as $s) {
			$sid = (int)$s->getId(); /** @var int $sid */
			GetResults::p($sid, false);
			if (1 > $limit) {
				break;
			}
			$limit = self::sendStoreSubscriberBatch($sid, $limit);
		}
		GetResults::p(0, false);
		if (0 < $limit) {
			self::sendStoreSubscriberBatch(0, $limit);
		}
	}

	/**
	 * Send Subscribers batch on particular store view, return batch response.
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by self::p()
	 */
	private static function sendStoreSubscriberBatch(int $sid, int $limit):int {
		$h = hcg_mc_h();
		try {
			if ($h->isSubscriptionEnabled($sid)) {
				$h->resetCountersSubscribers();
				$listId = $h->getGeneralList($sid);
				$batchArray = [];
				$api = new Api; /** @var Api $api */
				$subscribersArray = $api->createBatchJson($listId, $sid, $limit);
				$limit -= count($subscribersArray);
				$batchArray['operations'] = $subscribersArray;
				if (!empty($batchArray['operations'])) {
					$batchJson = json_encode($batchArray);
					if ($batchJson === false) {
						$h->logRequest('Json encode error ' . json_last_error_msg());
					}
					elseif ($batchJson == '') {
						$h->logRequest('An empty operation was detected');
					}
					else {
						try {
							$mailchimpApi = $h->getApi($sid);
							$batchResponse = $mailchimpApi->getBatchOperation()->add($batchJson);
							$h->logRequest($batchJson, $batchResponse['id']);
							$batch = new Synchbatches;
							$batch->setStoreId($sid)
								->setBatchId($batchResponse['id'])
								->setStatus($batchResponse['status']);
							$batch->save();
							self::_showResumeSubscriber($batchResponse['id'], $sid);
							return $limit;
						}
						catch (EApiKey $e) {
							$h->logError($e->getMessage());
						}
						catch (\MailChimp_Error $e) {
							$h->logRequest($batchJson);
							$h->logError($e->getFriendlyMessage());
						}
					}
				}
			}
		}
		catch (\Exception $e) {
			# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "`Ebizmarts_MailChimp_Model_Api_Batches::sendStoreSubscriberBatch()` should log the trace for exceptions":
			# https://github.com/thehcginstitute-com/m1/issues/509
			df_log($e);
		}
		return $limit;
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::sendStoreSubscriberBatch()
	 * @param $storeId
	 * @throws \Mage_Core_Exception
	 */
	private static function _showResumeSubscriber(string $batchId, $storeId):void	{
		$h = hcg_mc_h();
		$countersSubscribers = $h->getCountersSubscribers();
		if (!empty($countersSubscribers) || $h->getCountersSubscribers() != null) {
			$h->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$h->logBatchQuantity($h->getCountersSubscribers());
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $storeId");
		}
	}
}