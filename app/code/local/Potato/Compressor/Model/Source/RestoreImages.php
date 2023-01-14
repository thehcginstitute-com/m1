<?php

class Potato_Compressor_Model_Source_RestoreImages extends Mage_Core_Model_Config_Data
{
    protected function _afterSave()
    {
        foreach (Potato_Compressor_Helper_Config::getSkippedImages() as $path) {
            try {
                //restore all images from 'Do not optimize these images' field
                Mage::helper('po_compressor')->restoreImage($path);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        //clear images collection cache
        Mage::helper('po_compressor')->removeImageGalleryCache();
        return parent::_afterSave();
    }
}