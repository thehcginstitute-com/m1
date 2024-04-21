<?php
# 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
final class Ebizmarts_MailChimp_Model_Api_Batches {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_sendEcommerceBatch()
	 * @param $batchArray
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws Ebizmarts_MailChimp_Helper_Data_ApiKeyException
	 * @throws Mage_Core_Exception
	 * @throws MailChimp_Error
	 * @throws MailChimp_HttpError
	 */
	function _processBatchOperations($batchArray, $mailchimpStoreId, $magentoStoreId):void {
		$h = hcg_mc_h();
		$mailchimpApi = $h->getApi($magentoStoreId);
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
				$batch->setStoreId($mailchimpStoreId)->setBatchId($batchResponse['id'])->setStatus($batchResponse['status']);
				$batch->save();
				$this->markItemsAsSent($batchResponse['id'], $mailchimpStoreId);
				$this->_showResumeEcommerce($batchResponse['id'], $magentoStoreId);
			}
		}
		else {
			$h->logBatchStatus("Nothing to sync for store $magentoStoreId");
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_processBatchOperations()
	 * @param $batchId
	 * @param $storeId
	 * @throws Mage_Core_Exception
	 */
	private function _showResumeEcommerce($batchId, $storeId):void {
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

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_processBatchOperations()
	 */
	private function markItemsAsSent($batchResponseId, $mailchimpStoreId):void {
		$resource = hcg_mc_h()->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$where = array("batch_id IS NULL AND mailchimp_store_id = ?" => $mailchimpStoreId);
		$connection->update(
			$tableName,
			array(
				'batch_id' => $batchResponseId,
				'mailchimp_sync_delta' => hcg_mc_h_date()->formatDate(null, 'Y-m-d H:i:s')
			),
			$where
		);
	}
}