<?php

class Potato_Compressor_Model_Compressor_Cron
{
    const PROCESS_STEP = 25;

    /**
     * start image optimization
     *
     * @return $this
     */
    public function process()
    {
        if (!Potato_Compressor_Helper_Config::isEnabled() ||
            !Potato_Compressor_Helper_Config::isImageCronEnabled()
        ) {
            return $this;
        }

        $imageCollection = $this->_getImageGalleryFiles();
        $counter = 0;
        foreach ($imageCollection as $image) {
            try {
                Mage::getSingleton('po_compressor/compressor_image')->optimizeImage($image);
                $counter++;
                if ($counter == self::PROCESS_STEP) {
                    break;
                }
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    /**
     * get images collection
     *
     * @return mixed
     */
    protected function _getImageGalleryFiles()
    {
        return Mage::helper('po_compressor')->getImageGalleryFiles();
    }
}