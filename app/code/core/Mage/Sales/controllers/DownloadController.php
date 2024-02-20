<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales controller for download purposes
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_DownloadController extends Mage_Core_Controller_Front_Action
{
    /**
     * Custom options downloader
     *
     * @param mixed $info
     */
    protected function _downloadFileAction($info)
    {
        $secretKey = $this->getRequest()->getParam('key');
        try {
            if ($secretKey != $info['secret_key']) {
                throw new Exception();
            }

            $this->_validateFilePath($info);

            $filePath = Mage::getBaseDir() . $info['order_path'];
            if ((!is_file($filePath) || !is_readable($filePath)) && !$this->_processDatabaseFile($filePath)) {
                //try get file from quote
                $filePath = Mage::getBaseDir() . $info['quote_path'];
                if ((!is_file($filePath) || !is_readable($filePath)) && !$this->_processDatabaseFile($filePath)) {
                    throw new Exception();
                }
            }
            $this->_prepareDownloadResponse($info['title'], [
               'value' => $filePath,
               'type'  => 'filename'
            ]);
        } catch (Exception $e) {
            $this->_forward('noRoute');
        }
    }

    /**
     * @param array $info
     * @throws Exception
     */
    protected function _validateFilePath($info)
    {
        $optionFile = Mage::getModel('catalog/product_option_type_file');
        $optionStoragePath = $optionFile->getOrderTargetDir(true);
        if (strpos($info['order_path'], $optionStoragePath) !== 0) {
            throw new Exception('Unexpected file path');
        }
    }

    /**
     * Check file in database storage if needed and place it on file system
     *
     * @param string $filePath
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ErrorControlOperator)
     */
    protected function _processDatabaseFile($filePath)
    {
        if (!Mage::helper('core/file_storage_database')->checkDbUsage()) {
            return false;
        }

        $relativePath = Mage::helper('core/file_storage_database')->getMediaRelativePath($filePath);
        $file = Mage::getModel('core/file_storage_database')->loadByFilename($relativePath);

        if (!$file->getId()) {
            return false;
        }

        $directory = dirname($filePath);
        @mkdir($directory, 0777, true);

        $io = new Varien_Io_File();
        $io->cd($directory);

        $io->streamOpen($filePath);
        $io->streamLock(true);
        $io->streamWrite($file->getContent());
        $io->streamUnlock();
        $io->streamClose();

        return true;
    }

	# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401

    /**
     * Custom options download action
     */
    public function downloadCustomOptionAction()
    {
        $quoteItemOptionId = $this->getRequest()->getParam('id');
        /** @var Mage_Sales_Model_Quote_Item_Option $option */
        $option = Mage::getModel('sales/quote_item_option')->load($quoteItemOptionId);

        if (!$option->getId()) {
            $this->_forward('noRoute');
            return;
        }

        $optionId = null;
        if (strpos($option->getCode(), Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX) === 0) {
            $optionId = str_replace(Mage_Catalog_Model_Product_Type_Abstract::OPTION_PREFIX, '', $option->getCode());
            if (!is_numeric($optionId)) {
                $optionId = null;
            }
        }
        $productOption = null;
        if ($optionId) {
            /** @var Mage_Catalog_Model_Product_Option $productOption */
            $productOption = Mage::getModel('catalog/product_option')->load($optionId);
        }
        if (!$productOption || !$productOption->getId()
            || $productOption->getProductId() != $option->getProductId() || $productOption->getType() != 'file'
        ) {
            $this->_forward('noRoute');
            return;
        }

        try {
            $info = Mage::helper('core/unserializeArray')->unserialize($option->getValue());
            $this->_downloadFileAction($info);
        } catch (Exception $e) {
            $this->_forward('noRoute');
        }
        exit(0);
    }
}
