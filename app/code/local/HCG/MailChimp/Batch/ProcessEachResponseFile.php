<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Model_Config as Cfg;
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class ProcessEachResponseFile {
	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by GetResults::_saveItemStatus()
	 * @param $files
	 * @param $batchId
	 * @param $mailchimpStoreId
	 * @param $magentoStoreId
	 * @throws \Mage_Core_Exception
	 */
	static function p($files, $batchId, $mailchimpStoreId, $magentoStoreId):void {
		$h = hcg_mc_h();
		$h->resetCountersDataSentToMailchimp();
		$fileHelper = hcg_mc_h_file();
		$fileHelper->open(['path '=> hcg_mc_batches_path()]);
		foreach ($files as $file) {
			$fileContent = $fileHelper->read($file);
			$items = json_decode($fileContent, true);
			if ($items !== false) {
				foreach ($items as $item) {
					$line = explode('_', $item['operation_id']);
					$store = explode('-', $line[0]);
					$type = $line[1];
					$id = $line[3];
					if ($item['status_code'] != 200) {
						# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
						# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
						HandleErrorItem::p($item, $batchId, $mailchimpStoreId, $id, $type, $store);
					}
					else {
						$syncDataItem = hcg_mc_syncd_get((int)$id, $type, $mailchimpStoreId);
						if (!$syncDataItem->getMailchimpSyncModified()) {
							$syncModified = self::enableMergeFieldsSending($type, $syncDataItem);
							SaveSyncData::p(
								$id,
								$type,
								$mailchimpStoreId,
								null,
								'',
								$syncModified,
								null,
								null,
								1,
								true
							);
							$h->modifyCounterDataSentToMailchimp($type);
						}
						else {
							SaveSyncData::p(
								$id,
								$type,
								$mailchimpStoreId,
								null,
								'',
								0,
								null,
								null,
								1,
								true
							);
						}
					}
				}
			}
			$fileHelper->rm($file);
		}
		self::_showResumeDataSentToMailchimp($magentoStoreId);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::processEachResponseFile()
	 * @param $storeId
	 * @throws \Mage_Core_Exception
	 */
	private static function _showResumeDataSentToMailchimp($storeId):void {
		$h = hcg_mc_h();
		$countersDataSentToMailchimp = $h->getCountersDataSentToMailchimp();
		if (!empty($countersDataSentToMailchimp) || $h->getCountersDataSentToMailchimp() != null) {
			$h->logBatchStatus("Processed data sent to Mailchimp for store $storeId");
			$counter = $h->getCountersDataSentToMailchimp();
			$h->logBatchQuantity($counter);
			if (self::isSetAnyCounterSubscriberOrEcommerceNotSent($counter)) {
				if ($h->isErrorLogEnabled()) {
					$h->logBatchStatus(
						'Please check Mailchimp Errors grid or MailChimp_Errors.log for more details.'
					);
				}
				else {
					$h->logBatchStatus(
						'Please check Mailchimp Errors grid and enable MailChimp_Errors.log for more details.'
					);
				}
			}
		}
		else {
			$h->logBatchStatus("Nothing was processed for store $storeId");
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param $type
	 */
	private static function enableMergeFieldsSending($type, \Varien_Object $o):int {
		$syncModified = 0;
		if ($type == Cfg::IS_CUSTOMER && self::isFirstArrival($o)) {
			$syncModified = 1;
		}
		return $syncModified;
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::_showResumeDataSentToMailchimp()
	 */
	private static function isSetAnyCounterSubscriberOrEcommerceNotSent(array $counter):bool {return
		isset($counter['SUB']['NOT SENT'])
		|| isset($counter['CUS']['NOT SENT'])
		|| isset($counter['ORD']['NOT SENT'])
		|| isset($counter['PRO']['NOT SENT'])
		|| isset($counter['QUO']['NOT SENT'])
	;}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::enableMergeFieldsSending()
	 */
	private static function isFirstArrival(\Varien_Object $o):bool {return (int)$o->getMailchimpSyncedFlag() !== 1;}
}