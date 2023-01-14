<?php

class Potato_Compressor_Helper_Data extends Mage_Core_Helper_Abstract
{
    const MAIN_FOLDER = 'po_compressor';
    const GALLERY_CACHE_ID = 'po_compressor_GALLERY_CACHE_ID';
    const GALLERY_CACHE_LIFETIME = 1800;

    /**
     * @return string
     */
    public function getRootCachePath()
    {
        return Mage::getBaseDir('media') . DS. self::MAIN_FOLDER;
    }

    /**
     * @return string
     */
    public function getRootCacheUrl()
    {
        return Mage::getBaseUrl('media') . self::MAIN_FOLDER;
    }

    /**
     * @return $this
     */
    public function clearCache()
    {
        $this->_removeFolder($this->getRootCachePath());
        return $this;
    }

    /**
     * @param $dirPath
     *
     * @return bool
     */
    protected function _removeFolder($dirPath)
    {
        Varien_Io_File::rmdirRecursive($dirPath);
        return true;
    }

    /**
     * @return array
     */
    public function getImageGalleryFiles()
    {
        $images = $this->_loadImageGalleryCache();
        if (!is_array($images) || empty($images)) {
            $optimizedImagesHashes = Mage::getModel('po_compressor/image')->getCollection()->getColumnValues('hash');
            $images = array_merge($this->_getImagesFromDir(Mage::getBaseDir('media'), $optimizedImagesHashes),
                $this->_getImagesFromDir(self::getSkinDir(), $optimizedImagesHashes)
            );
            $this->_saveImageGalleryCache($images);
        }
        return $images;
    }

    /**
     * @param $images
     *
     * @return bool
     */
    protected function _saveImageGalleryCache($images)
    {
        Mage::app()->saveCache(@serialize($images), self::GALLERY_CACHE_ID, array(), self::GALLERY_CACHE_LIFETIME);
        return true;
    }

    /**
     * @return bool|mixed
     */
    protected function _loadImageGalleryCache()
    {
        $data = Mage::app()->loadCache(self::GALLERY_CACHE_ID);
        if ($data) {
            return unserialize($data);
        }
        return false;
    }

    /**
     * @return $this
     */
    public function removeImageGalleryCache()
    {
        Mage::app()->removeCache(self::GALLERY_CACHE_ID);
        return $this;
    }

    /**
     * @return string
     */
    static function getSkinDir()
    {
        return Mage::getBaseDir('skin') . DS . 'frontend';
    }

    /**
     * @param $dirPath
     * @param $optimizedImagesHashes
     *
     * @return array
     */
    protected function _getImagesFromDir($dirPath, $optimizedImagesHashes)
    {
        $findedFiles = array_diff(scandir($dirPath),
            array (
                '..',
                '.',
                '.htaccess',
                Potato_Compressor_Model_Compressor_Image::MEDIA_ORIGINAL_FOLDER_NAME,
                Potato_Compressor_Model_Compressor_Image::SKIN_ORIGINAL_FOLDER_NAME
            )
        );
        $_result = array();
        foreach ($findedFiles as $file) {
            if (is_dir($dirPath . DS . $file)) {
                $_result = array_merge($_result, $this->_getImagesFromDir($dirPath . DS . $file, $optimizedImagesHashes));
                continue;
            }
            if (!@getimagesize($dirPath . DS . $file) ||
                in_array($this->getImageHash($dirPath . DS . $file), $optimizedImagesHashes) ||
                $this->isIgnoredImage($dirPath . DS . $file)
            ) {
                continue;
            }
            array_push($_result, $dirPath . DS . $file);
        }
        return $_result;
    }

    /**
     * @param $image
     *
     * @return bool
     */
    public function isOptimizedImage($image)
    {
        $optimizedImage = Mage::getModel('po_compressor/image')->loadByHash($this->getImageHash($image));
        if ($optimizedImage->getId()) {
            return true;
        }
        return false;
    }

    /**
     * @param $image
     *
     * @return string
     */
    public function getImageHash($image)
    {
        return md5($image . file_get_contents($image));
    }

    /**
     * @param $image
     *
     * @return bool
     */
    public function isIgnoredImage($image)
    {
        $path = str_replace(BP . DS, '', $image);
        if (in_array($path, Potato_Compressor_Helper_Config::getSkippedImages())) {
            return true;
        }
        return false;
    }

    /**
     * @param $folderPath
     *
     * @return mixed
     */
    static function prepareFolder($folderPath)
    {
        return Mage::getConfig()->createDirIfNotExists($folderPath);
    }

    /**
     * @param $path
     *
     * @return bool
     */
    static function createHtaccessFile($path)
    {
        $content = "<ifmodule mod_deflate.c>\n"
            . "AddOutputFilterByType DEFLATE text/html text/plain text/css application/json\n"
            . "AddOutputFilterByType DEFLATE application/javascript\n"
            . "AddOutputFilterByType DEFLATE text/xml application/xml text/x-component\n"
            . "AddOutputFilterByType DEFLATE application/xhtml+xml application/rss+xml application/atom+xml\n"
            . "AddOutputFilterByType DEFLATE image/svg+xml application/vnd.ms-fontobject application/x-font-ttf font/opentype\n"
            . "SetOutputFilter DEFLATE\n"
            . "</ifmodule>\n"
            . "<ifmodule mod_headers.c>\n"
            . "<FilesMatch '\.(css|js|jpe?g|png|gif)$'>\n"
            . "Header set Cache-Control 'max-age=2592000, public'\n"
            . "</FilesMatch>\n"
            . "</ifmodule>\n"
            . "<ifmodule mod_expires.c>\n"
            . "ExpiresActive On\n"
            . "ExpiresByType text/css 'access plus 30 days'\n"
            . "ExpiresByType text/javascript 'access plus 30 days'\n"
            . "ExpiresByType application/x-javascript 'access plus 30 days'\n"
            . "ExpiresByType image/jpeg 'access plus 30 days'\n"
            . "ExpiresByType image/png 'access plus 30 days'\n"
            . "ExpiresByType image/gif 'access plus 30 days'\n"
            . "</ifmodule>\n"
        ;
        file_put_contents($path . DS . '.htaccess', $content);
        return true;
    }

    /**
     * @param $image
     *
     * @return bool
     */
    static function isMediaImage($image)
    {
        return strpos($image, Mage::getBaseUrl('media', Mage::app()->getRequest()->isSecure())) !== FALSE;
    }

    /**
     * @param $image
     *
     * @return bool
     */
    static function isSkinImage($image)
    {
        return strpos($image, Mage::getBaseUrl('skin', Mage::app()->getRequest()->isSecure())) !== FALSE;
    }

    /**
     * @param $image
     *
     * @return $this
     */
    public function restoreImage($image)
    {
        $backupImg = $this->getBackupImagePath($image);
        if ($backupImg && file_exists($backupImg)) {
            $content = file_get_contents($backupImg);
            file_put_contents($image, $content);
        }
        return $this;
    }

    /**
     * @param $image
     *
     * @return bool|string
     */
    public function getBackupImagePath($image)
    {
        $path = false;
        if (strpos($image, Mage::getBaseDir('media')) !== FALSE) {
            $path = rtrim(Mage::getBaseDir('media'), DS)
                . DS . Potato_Compressor_Model_Compressor_Image::MEDIA_ORIGINAL_FOLDER_NAME
                . DS . str_replace(Mage::getBaseDir('media'), '', $image)
            ;
        }
        if (!$path && strpos($image, Potato_Compressor_Helper_Data::getSkinDir()) !== FALSE) {
            $path = rtrim(Potato_Compressor_Helper_Data::getSkinDir(), DS)
                . DS . Potato_Compressor_Model_Compressor_Image::SKIN_ORIGINAL_FOLDER_NAME
                . DS . str_replace(Potato_Compressor_Helper_Data::getSkinDir(), '', $image)
            ;
        }
        return $path;
    }

    public function encode($value)
    {
        //!do not use urlEncode function - grid mass actions will be work incorrect
        return base64_encode($value);
    }

    public function decode($value)
    {
        return base64_decode($value);
    }

    static function canScaleImage($imageSrc)
    {
        if (in_array($imageSrc, Potato_Compressor_Helper_Config::getScalingImages())) {
            return true;
        }
        return false;
    }
}