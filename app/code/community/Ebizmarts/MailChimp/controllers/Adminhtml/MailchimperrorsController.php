<?php

/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     6/10/16 12:35 PM
 * @file:     MailchimperrorsController.php
 */
class Ebizmarts_MailChimp_Adminhtml_MailchimperrorsController extends Mage_Adminhtml_Controller_Action
{
	const MAX_RETRIES = 5;

	function indexAction()
	{
		$this->_title($this->__('Newsletter'))
			->_title($this->__('MailChimp'));

		$this->loadLayout();
		$this->_setActiveMenu('newsletter/mailchimp');
		$this->renderLayout();
	}

	function gridAction()
	{
		$this->loadLayout(false);
		$this->renderLayout();
	}

	function downloadresponseAction()
	{
		$helper = $this->makeHelper();
		$errorId = $this->getRequest()->getParam('id');
		$error = $this->getMailchimperrorsModel()->load($errorId);
		$apiBatches = $this->getApiBatches();
		$batchId = $error->getBatchId();
		$storeId = $error->getStoreId();
		$mailchimpStoreId = $error->getMailchimpStoreId();

		if ($mailchimpStoreId) {
			$enabled = $helper->isEcomSyncDataEnabled($storeId);
		} else {
			$enabled = $helper->isSubscriptionEnabled($storeId);
		}

		if ($enabled) {
			$response = $this->getResponse();
			$response->setHeader('Content-disposition', 'attachment; filename=' . $batchId . '.json');
			$response->setHeader('Content-type', 'application/json');
			$counter = 0;

			do {
				$counter++;
				$files = $apiBatches->getBatchResponse($batchId, $storeId);
				$fileContent = array();
				if (array_key_exists('error', $files)) {
					$fileContent = $this->__("Response was deleted from MailChimp server.");
					break;
				}

				foreach ($files as $file) {
					$items = $this->getFileContent($file);

					foreach ($items as $item) {
						$fileContent[] = array(
							'status_code' => $item['status_code'],
							'operation_id' => $item['operation_id'],
							'response' => json_decode($item['response'])
						);
					}

					$this->unlink($file);
				}

				$baseDir = $apiBatches->getMagentoBaseDir();

				if ($apiBatches->batchDirExists($baseDir, $batchId)) {
					$apiBatches->removeBatchDir($baseDir, $batchId);
				}
			} while (!count($fileContent) && $counter < self::MAX_RETRIES);

			$response->setBody(json_encode($fileContent, JSON_PRETTY_PRINT));
		}

		return;
	}

	/**
	 * 2024-03-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	 * "Resolve the `Ebizmarts_MailChimp` module's issues found by IntelliJ IDEA inspections":
	 * https://github.com/thehcginstitute-com/m1/issues/530
	 * @override
	 * @see Mage_Adminhtml_Controller_Action::_isAllowed()
	 */
	protected function _isAllowed():bool {return in_array($this->getRequest()->getActionName(), [
		'downloadresponse', 'grid', 'index'
	]) && $this->getAdminSession()->isAllowed('newsletter/mailchimp/mailchimperrors');}
	/**
	 * @return Ebizmarts_MailChimp_Helper_Data
	 */
	protected function makeHelper()
	{
		return Mage::helper('mailchimp');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Mailchimperrors
	 */
	protected function getMailchimperrorsModel()
	{
		return Mage::getModel('mailchimp/mailchimperrors');
	}

	/**
	 * @return Ebizmarts_MailChimp_Model_Api_Batches
	 */
	protected function getApiBatches()
	{
		return Mage::getModel('mailchimp/api_batches');
	}

	/**
	 * @param string $file
	 * @return stdClass
	 */
	protected function getFileContent($file)
	{
		$fileContent = $this->getFileHelper()->read($file);

		return json_decode($fileContent, true);
	}

	/**
	 * @param string $file
	 */
	protected function unlink($file)
	{
		return $this->getFileHelper()->unlink($file);
	}

	/**
	 * @return Ebizmarts_MailChimp_Helper_File
	 */
	protected function getFileHelper()
	{
		return Mage::helper('mailchimp/file');
	}
}
