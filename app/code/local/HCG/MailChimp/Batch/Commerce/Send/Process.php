<?php
namespace HCG\MailChimp\Batch\Commerce\Send;
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
# 2024-04-22 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class Process {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by \HCG\MailChimp\Batch\Commerce\Send::p()
	 * @param $batchArray
	 * @throws \Ebizmarts_MailChimp_Helper_Data_ApiKeyException
	 * @throws \Mage_Core_Exception
	 * @throws \MailChimp_Error
	 * @throws \MailChimp_HttpError
	 */
	static function p($batchArray, string $mcStore, int $mgStore):void {
		$h = hcg_mc_h();
		$mailchimpApi = $h->getApi($mgStore);
		if (!empty($batchArray['operations'])) {
			$batchJson = json_encode($batchArray);
			if ($batchJson === false) {
				$h->logRequest('Json encode error ' . json_last_error_msg());
			}
			elseif (empty($batchJson)) {
				$h->logRequest('An empty operation was detected');
			}
			else {
				$h->logBatchStatus('Send batch operation');
				$batchResponse = $mailchimpApi->getBatchOperation()->add($batchJson);
				$h->logRequest($batchJson, $batchResponse['id']);
				//save batch id to db
				$batch = new Synchbatches;
				$batch->setStoreId($mcStore)->setBatchId($batchResponse['id'])->setStatus($batchResponse['status']);
				$batch->save();
				self::markItemsAsSent($batchResponse['id'], $mcStore);
				self::_showResumeEcommerce($batchResponse['id'], $mgStore);
			}
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $mgStore");
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 */
	private static function markItemsAsSent($batchResponseId, string $mcStore):void {
		$resource = hcg_mc_h()->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = ["batch_id IS NULL AND mailchimp_store_id = ?" => $mcStore];
		$connection->update(
			$tableName,
			[
				'batch_id' => $batchResponseId,
				'mailchimp_sync_delta' => hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s')
			],
			$where
		);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param $storeId
	 * @throws \Mage_Core_Exception
	 */
	private static function _showResumeEcommerce(string $batchId, $storeId):void {
		$h = hcg_mc_h();
		$countersSentPerBatch = $h->getCountersSentPerBatch();
		if (!empty($countersSentPerBatch) || $countersSentPerBatch != null) {
			$h->logBatchStatus("Sent batch $batchId for Magento store $storeId");
			$h->logBatchQuantity($h->getCountersSentPerBatch());
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $storeId");
		}
	}
}