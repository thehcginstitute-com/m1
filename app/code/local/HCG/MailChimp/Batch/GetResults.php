<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Model_Api_Batches as Sb;
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class GetResults {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::handleEcommerceBatches()
	 * @used-by Subscriber::p()
	 * @param $magentoStoreId
	 * @throws \Mage_Core_Exception
	 */
	static function p(
		$magentoStoreId, bool $isEcommerceData = true, $status = \Ebizmarts_MailChimp_Helper_Data::BATCH_PENDING
	) {
		$h = hcg_mc_h();
		$mailchimpStoreId = $h->getMCStoreId($magentoStoreId);
		$syncb = new Synchbatches;
		$collection = $syncb->getCollection()->addFieldToFilter('status', ['eq' => $status]);
		if ($isEcommerceData) {
			$collection->addFieldToFilter('store_id', ['eq' => $mailchimpStoreId]);
			$enabled = $h->isEcomSyncDataEnabled($magentoStoreId);
		}
		else {
			$collection->addFieldToFilter('store_id', ['eq' => $magentoStoreId]);
			$enabled = $h->isSubscriptionEnabled($magentoStoreId);
		}
		if ($enabled) {
			$h->logBatchStatus('Get results from Mailchimp for Magento store ' . $magentoStoreId);
			foreach ($collection as $item) {
				try {
					$batchId = $item->getBatchId();
					$files = GetBatchResponse::p($batchId, $magentoStoreId);
					self::_saveItemStatus($item, $files, $batchId, $mailchimpStoreId, $magentoStoreId);
					hcg_mc_batch_delete($batchId);
				}
				catch (\Exception $e) {
					\Mage::log("Error with a response: " . $e->getMessage());
				}
			}
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param $item
	 * @param $files
	 * @param $batchId
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws \Mage_Core_Exception
	 */
	private static function _saveItemStatus($item, $files, $batchId, $mailchimpStoreId, $magentoStoreId):void {
		$h = hcg_mc_h();
		if (!empty($files)) {
			if (isset($files['error'])) {
				$item->setStatus('error');
				$item->save();
				$h->logBatchStatus('There was an error getting the result ');
			}
			else {
				ProcessEachResponseFile::p($files, $batchId, $mailchimpStoreId, $magentoStoreId);
				$item->setStatus('completed');
				$item->save();
			}
		}
	}
}