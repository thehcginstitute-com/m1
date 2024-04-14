<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
namespace HCG\MailChimp\Model\Api;
use Ebizmarts_MailChimp_Model_Api_Batches as Sb;
final class Batches {
	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by Ebizmarts_MailChimp_Model_Api_Batches::processEachResponseFile()
	 */
	static function handleErrorItem(Sb $sb, array $i, $batchId, $mailchimpStoreId, $id, $type, $store):void {
		$mailchimpErrors = \Mage::getModel('mailchimp/mailchimperrors');
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
			# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# «Undefined index: type in app/code/community/Ebizmarts/MailChimp/Model/Api/Batches.php
			# on line 836»: https://github.com/thehcginstitute-com/m1/issues/510
			$mailchimpErrors->setType(dfa($response, 'type'));
			$mailchimpErrors->setTitle($response['title']);
			$mailchimpErrors->setStatus($i['status_code']);
			$mailchimpErrors->setErrors($errorDetails);
			$mailchimpErrors->setRegtype($type);
			$mailchimpErrors->setOriginalId($id);
			$mailchimpErrors->setBatchId($batchId);
			$mailchimpErrors->setStoreId($store[1]);
			if ($type != \Ebizmarts_MailChimp_Model_Config::IS_SUBSCRIBER) {
				$mailchimpErrors->setMailchimpStoreId($mailchimpStoreId);
			}
			$mailchimpErrors->save();
			hcg_mc_h()->modifyCounterDataSentToMailchimp($type, true);
			hcg_mc_h()->logError($error);
		}
	}
}