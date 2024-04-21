<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Model_Synchbatches as Synchbatches;
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class GetResults {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by Commerce::p()
	 * @used-by Subscriber::p()
	 * @param $mgStore
	 * @throws \Mage_Core_Exception
	 */
	static function p(
		$mgStore, bool $isEcommerceData = true, $status = \Ebizmarts_MailChimp_Helper_Data::BATCH_PENDING
	) {
		$h = hcg_mc_h();
		$mcStore = $h->getMCStoreId($mgStore);
		$syncb = new Synchbatches;
		$collection = $syncb->getCollection()->addFieldToFilter('status', ['eq' => $status]);
		if ($isEcommerceData) {
			$collection->addFieldToFilter('store_id', ['eq' => $mcStore]);
			$enabled = $h->isEcomSyncDataEnabled($mgStore);
		}
		else {
			$collection->addFieldToFilter('store_id', ['eq' => $mgStore]);
			$enabled = $h->isSubscriptionEnabled($mgStore);
		}
		if ($enabled) {
			$h->logBatchStatus('Get results from Mailchimp for Magento store ' . $mgStore);
			foreach ($collection as $item) {
				try {
					$batchId = $item->getBatchId();
					self::_saveItemStatus($item, GetBatchResponse::p($batchId, $mgStore), $batchId, $mcStore, $mgStore);
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
	 * @param $mcStore
	 * @param $mgStore
	 * @throws \Mage_Core_Exception
	 */
	private static function _saveItemStatus($item, $files, $batchId, $mcStore, $mgStore):void {
		$h = hcg_mc_h();
		if (!empty($files)) {
			if (isset($files['error'])) {
				$item->setStatus('error');
				$item->save();
				$h->logBatchStatus('There was an error getting the result ');
			}
			else {
				ProcessEachResponseFile::p($files, $batchId, $mcStore, $mgStore);
				$item->setStatus('completed');
				$item->save();
			}
		}
	}
}