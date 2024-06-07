<?php
namespace HCG\MailChimp\Batch;
# 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
final class GetBatchResponse {
	/**
	 * 2024-04-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Refactor `Ebizmarts_MailChimp_Model_Api_Batches::getBatchResponse()`":
	 * https://github.com/thehcginstitute-com/m1/issues/571
	 * @used-by Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController::downloadresponseAction()
	 * @used-by GetResults::p()
	 */
	static function p(string $batchId, int $mgStore):array {
		$h = hcg_mc_h();
		$fileHelper = hcg_mc_h_file();
		$r = []; /** @var array $r */
		try {
			$api = $h->getApi($mgStore);
			if ($fileHelper->isDir(hcg_mc_batches_path()) == false) {
				$fileHelper->mkDir(hcg_mc_batches_path());
			}
			if ($api) {
				// check the status of the job
				$response = $api->batchOperation->status($batchId);
				if (isset($response['status']) && $response['status'] == 'finished') {
					// get the tar.gz file with the results
					$fileUrl = urldecode($response['response_body_url']);
					$fileName = hcg_mc_batches_path($batchId) . '.tar.gz';
					$fd = fopen($fileName, 'w');
					$curlOptions = [
						CURLOPT_RETURNTRANSFER => 1,
						CURLOPT_FILE => $fd,
						CURLOPT_FOLLOWLOCATION => true, // this will follow redirects
						CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					];
					hcg_mc_h_curl()->curlExec($fileUrl, \Zend_Http_Client::GET, $curlOptions);
					fclose($fd);
					$fileHelper->mkDir(hcg_mc_batches_path($batchId), 0750, true);
					$archive = new \Mage_Archive();
					if ($fileHelper->fileExists($fileName)) {
						$r = self::_unpackBatchFile($r, $batchId, $archive, $fileName);
					}
				}
			}
		}
		catch (\Ebizmarts_MailChimp_Helper_Data_ApiKeyException $e) {
			df_log($e);
			$r['error'] = $e->getMessage();
		}
		catch (\MailChimp_Error $e) {
			self::deleteBatchItems($batchId);
			$r['error'] = df_xts($e);
			df_log($e);
		}
		catch (\Exception $e) {
			$r['error'] = $e->getMessage();
			df_log($e);
		}
		return $r;
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 */
	private static function deleteBatchItems(string $batchId):void {
		$resource = hcg_mc_h()->getCoreResource();
		$connection = $resource->getConnection('core_write');
		$tableName = $resource->getTableName('mailchimp/ecommercesyncdata');
		$connection->delete($tableName, ["batch_id = '$batchId'"]);
	}

	/**
	 * 2024-04-21 "Refactor `Ebizmarts_MailChimp_Model_Api_Batches`": https://github.com/thehcginstitute-com/m1/issues/572
	 * @used-by self::p()
	 * @param $files
	 * @param $fileName
	 */
	private static function _unpackBatchFile($files, string $batchId, \Mage_Archive $archive, $fileName):array {
		$path = hcg_mc_batches_path($batchId);
		$archive->unpack($fileName, $path);
		$archive->unpack($path . DS . $batchId . '.tar', $path);
		$fileHelper = hcg_mc_h_file();
		$dirItems = new \DirectoryIterator($path);
		foreach ($dirItems as $dirItem) {
			if ($dirItem->isFile() && $dirItem->getExtension() == 'json') {
				$files[] = $path . DS . $dirItem->getBasename();
			}
		}
		$fileHelper->rm($path . DS . $batchId . '.tar');
		$fileHelper->rm($fileName);
		return $files;
	}
}