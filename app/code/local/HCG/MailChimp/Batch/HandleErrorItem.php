<?php
namespace HCG\MailChimp\Batch;
use Ebizmarts_MailChimp_Model_Api_Products as ApiProducts;
use Ebizmarts_MailChimp_Model_Config as Cfg;
use Ebizmarts_MailChimp_Model_Mailchimperrors as mE;
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class HandleErrorItem {
	/**
	 * 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor the `Ebizmarts_MailChimp` module": https://github.com/thehcginstitute-com/m1/issues/524
	 * @used-by ProcessEachResponseFile::p()
	 */
	static function p(array $i, $batchId, $mailchimpStoreId, $id, $type, $store):void {
		$res = json_decode($i['response'], true);
		$errorDetails = self::processFileErrors($res);
		if (strstr($errorDetails, 'already exists')) {
			self::setItemAsModified($mailchimpStoreId, $id, $type);
			hcg_mc_h()->modifyCounterDataSentToMailchimp($type);
		}
		else {
			$error = self::error($type, $mailchimpStoreId, $id, $res);
			SaveSyncData::p(
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
				,'title' => $res['title']
				# 2024-03-17 Dmitrii Fediuk https://upwork.com/fl/mage2pro
				# «Undefined index: type in app/code/community/Ebizmarts/MailChimp/Model/Api/Batches.php
				# on line 836»: https://github.com/thehcginstitute-com/m1/issues/510
				,'type' => dfa($res, 'type')
			]); /** @var mE $mE */
			if ($type != Cfg::IS_SUBSCRIBER) {
				$mE['mailchimp_store_id'] = $mailchimpStoreId;
			}
			$mE->save();
			hcg_mc_h()->modifyCounterDataSentToMailchimp($type, true);
			# 2024-04-14 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# "Improve the error logging in `HCG\MailChimp\Batch\HandleErrorItem::p()`":
			# https://github.com/thehcginstitute-com/m1/issues/565
			df_log($error, null, $mE->getData());
		}
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
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

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param array(string => mixed) $p
	 */
	private static function processFileErrors(array $p):string {
		$r = ''; /** @var string $r */
		if (!empty($p['errors'])) {
			foreach ($p['errors'] as $error) {
				if (isset($error['field']) && isset($error['message'])) {
					$r .= $r != "" ? " / " : "";
					$r .= $error['field'] . " : " . $error['message'];
				}
			}
		}
		if ($r == "") {
			$r = $p['detail'];
		}
		return $r;
	}

	/**
	 * @used-by self::p()
	 * @param $mailchimpStoreId
	 * @param $id
	 * @param $type
	 */
	private static function setItemAsModified($mailchimpStoreId, $id, $type):void {
		if ($type == Cfg::IS_PRODUCT) {
			$dataProduct = hcg_mc_syncd_get((int)$id, $type, $mailchimpStoreId);
			$isMarkedAsDeleted = $dataProduct->getMailchimpSyncDeleted();
			$isProductDisabledInMagento = ApiProducts::PRODUCT_DISABLED_IN_MAGENTO;
			if (!$isMarkedAsDeleted || $dataProduct['mailchimp_sync_error']!= $isProductDisabledInMagento) {
				SaveSyncData::p(
					$id,
					$type,
					$mailchimpStoreId,
					null,
					null,
					1,
					0,
					null,
					1,
					true
				);
			}
			else {
				SaveSyncData::p(
					$id,
					$type,
					$mailchimpStoreId,
					null,
					$isProductDisabledInMagento,
					0,
					1,
					null,
					0,
					true
				);
			}
		}
		else {
			SaveSyncData::p(
				$id,
				$type,
				$mailchimpStoreId,
				null,
				null,
				1,
				0,
				null,
				1,
				true
			);
		}
	}
}