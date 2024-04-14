<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
namespace HCG\MailChimp\Model\Api;
use Ebizmarts_MailChimp_Model_Api_Batches as Sb;
use Ebizmarts_MailChimp_Model_Mailchimperrors as mE;
final class Batches {
	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::processEachResponseFile()
	 */
	static function handleErrorItem(Sb $sb, array $i, $batchId, $mailchimpStoreId, $id, $type, $store):void {
		$response = json_decode($i['response'], true);
		$errorDetails = $sb->_processFileErrors($response);
		if (strstr($errorDetails, 'already exists')) {
			$sb->setItemAsModified($mailchimpStoreId, $id, $type);
			hcg_mc_h()->modifyCounterDataSentToMailchimp($type);
		}
		else {
			$error = $sb->_getError($type, $mailchimpStoreId, $id, $response);
			$sb->saveSyncData(
				$id,
				$type,
				$mailchimpStoreId,
				null,
				$error,
				0,
				null,
				null,
				0,
				true
			);
			$mE = new mE([
				'batch_id' => $batchId
				,'errors' => $errorDetails
				,'original_id' => $id
				,'regtype' => $type
				,'status' => $i['status_code']
				,'store_id' => $store[1]
				,'title' => $response['title']
				# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# «Undefined index: type in app/code/community/Ebizmarts/MailChimp/Model/Api/Batches.php
				# on line 836»: https://github.com/thehcginstitute-com/m1/issues/510
				,'type' => dfa($response, 'type')
			]); /** @var mE $mE */
			if ($type != \Ebizmarts_MailChimp_Model_Config::IS_SUBSCRIBER) {
				$mE->setMailchimpStoreId($mailchimpStoreId);
			}
			$mE->save();
			hcg_mc_h()->modifyCounterDataSentToMailchimp($type, true);
			hcg_mc_h()->logError($error);
		}
	}
}