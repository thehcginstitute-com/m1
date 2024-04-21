<?php
# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
# 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
namespace HCG\MailChimp\Model\Api;
use Ebizmarts_MailChimp_Model_Api_Batches as Sb;
use Ebizmarts_MailChimp_Model_Api_Products as ApiProducts;
use Ebizmarts_MailChimp_Model_Config as Cfg;
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
			$error = self::error($type, $mailchimpStoreId, $id, $response);
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
			if ($type != Cfg::IS_SUBSCRIBER) {
				$mE['mailchimp_store_id'] = $mailchimpStoreId;
			}
			$mE->save();
			hcg_mc_h()->modifyCounterDataSentToMailchimp($type, true);
			# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Improve the error logging in `HCG\MailChimp\Model\Api\Batches::handleErrorItem()`":
			# https://github.com/thehcginstitute-com/m1/issues/565
			df_log($error, null, $mE->getData());
		}
	}

	/**
	 * 2023-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::handleErrorItem()
	 * @param $mailchimpStoreId
	 * @param $id
	 * @param $response
	 */
	private static function error($type, $mailchimpStoreId, $id, $response):string {
		$r = $response['title'] . " : " . $response['detail']; /** @var string $r */
		if ($type == Cfg::IS_PRODUCT) {
			$dataProduct = hcg_mc_syncd_get((int)$id, $type, $mailchimpStoreId);
			$isProductDisabledInMagento = ApiProducts::PRODUCT_DISABLED_IN_MAGENTO;
			if ($dataProduct->getMailchimpSyncDeleted()
				|| $dataProduct['mailchimp_sync_error'] == $isProductDisabledInMagento
			) {
				$r = $isProductDisabledInMagento;
			}
		}
		return $r;
	}
}