<?php

require_once Mage::getModuleDir('controllers', 'Mage_Adminhtml') . DS . 'System' . DS . 'ConfigController.php';
class Potato_Compressor_Adminhtml_Potato_Compressor_IndexController extends Mage_Adminhtml_System_ConfigController
{
    const CACHE_LIFETIME = 86400;

    public function optimizationAction()
    {
        $session = Mage::getSingleton('adminhtml/session');
        $response = array('progress' => 0);
        if(!function_exists('exec')) {
            $session->addError(Mage::helper('po_compressor')->__('Can\'t run process, please enable "exec" function'));
            $response['reload'] = 1;
            $response = Mage::helper('core')->jsonEncode($response);
            $this->getResponse()->setBody($response);
            return $this;
        }

        $compressorData = $this->_loadCache();
        if (!$compressorData) {
            $compressorData = new Varien_Object;

            $ids = $this->getRequest()->getParam('id', false);
            if ($ids) {
                $compressorData->setImageGallery(array_map('base64_decode', $ids));
            } else {
                $compressorData->setImageGallery(Mage::helper('po_compressor')->getImageGalleryFiles());
            }

            $compressorData->setImageGalleryCount(count($compressorData->getImageGallery()));
            $this->_saveCache($compressorData);
        } else {
            $imageGallery = $compressorData->getImageGallery();
            $counter = 0;
            foreach ($imageGallery as $key => $image) {
                try {
                    Mage::getSingleton('po_compressor/compressor_image')->optimizeImage($image);
                } catch (Exception $e) {
                    $session->addException($e,
                        Mage::helper('adminhtml')->__('An error occurred while saving this configuration:') . ' '
                        . $e->getMessage())
                    ;
                    $response['reload'] = 1;
                    $response = Mage::helper('core')->jsonEncode($response);
                    $this->getResponse()->setBody($response);
                    return $this;
                }
                $counter++;
                unset($imageGallery[$key]);
                if ($counter == 5) {
                    break;
                }
            }
            $compressorData->setImageGallery($imageGallery);
            $this->_saveCache($compressorData);
            $response['progress'] = 100 - floor((count($compressorData->getImageGallery()) / $compressorData->getImageGalleryCount()) * 100);
            $response['total'] = $compressorData->getImageGalleryCount();
            $response['current'] = $compressorData->getImageGalleryCount() - count($compressorData->getImageGallery());
            if (count($compressorData->getImageGallery()) == 0) {
                $this->_removeCache();
                $session->addSuccess(Mage::helper('po_compressor')->__('Image Optimization complete.'));
                $response['reload'] = 1;
            }
        }
        $response = Mage::helper('core')->jsonEncode($response);
        $this->getResponse()->setBody($response);
        return $this;
    }

    protected function _removeCache()
    {
        Mage::app()->removeCache(Mage::getSingleton('adminhtml/session')->getEncryptedSessionId());
        return $this;
    }

    public function flushAction()
    {
        try {
            Mage::getResourceModel('po_compressor/image')->truncate();
            Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('po_compressor')->__('The Compressor Images Cache has been cleaned.'));
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        $this->_redirectReferer();
    }

    protected function _saveCache($varienObject)
    {
        Mage::app()->saveCache(@serialize($varienObject->getData()), Mage::getSingleton('adminhtml/session')->getEncryptedSessionId(), array(), self::CACHE_LIFETIME);
        return true;
    }

    protected function _loadCache()
    {
        $data = Mage::app()->loadCache(Mage::getSingleton('adminhtml/session')->getEncryptedSessionId());
        if ($data) {
            return new Varien_Object(unserialize($data));
        }
        return false;
    }
}