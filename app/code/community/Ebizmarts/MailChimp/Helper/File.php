<?php

/**
 *
 * MailChimp For Magento
 *
 * Class Ebizmarts_MailChimp_Helper_Curl
 * @category  Ebizmarts_MailChimp
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     9/5/19 3:55 PM
 * @file:     Curl.php
 */

class Ebizmarts_MailChimp_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * @var $_ioFile Varien_Io_File
     */
    protected $_ioFile;

    function __construct()
    {
        $this->_ioFile = new Varien_Io_File();
    }

    /**
     * @return Varien_Io_File
     */
    function getIoFile()
    {
        return $this->_ioFile;
    }

    /**
     * @param string $file
     * @return string
     */
    function dirName($file = '.')
    {
        return $this->_ioFile->dirname($file);
    }
    /**
     * @param string $name
     * @return bool
     */
    function isDir($name)
    {
        return is_dir($name);
    }

    /**
     * @param $dir
     * @param int $mode
     * @param bool $recursive
     * @return bool
     */
    function mkDir($dir, $mode = 0777, $recursive = true)
    {
        return $this->_ioFile->mkdir($dir, $mode, $recursive);
    }

    /**
     * @param $directory
     * @param bool $recursive
     * @return bool
     */
    function rmDir($directory, $recursive = false)
    {
        return $this->_ioFile->rmdir($directory, $recursive);
    }

    /**
     * @param $filename
     * @param $src
     * @param null $mode
     * @return bool|int
     */
    function write($filename, $src, $mode = null)
    {
        return $this->_ioFile->write($filename, $src, $mode);
    }

    /**
     * @param $file
     * @param bool $onlyFile
     * @return bool
     */
    function fileExists($file, $onlyFile = true)
    {
        return $this->_ioFile->fileExists($file, $onlyFile);
    }

    /**
     * @param $filename
     * @return bool
     */
    function unlink($filename)
    {
        return $this->rm($filename);
    }

    /**
     * @param $filename
     * @return bool
     */
    function rm($filename)
    {
        return $this->_ioFile->rm($filename);
    }

    /**
     * @param $filename
     * @param $content
     * @return int
     */
    function filePutContent($filename, $content)
    {
        return $this->_ioFile->filePutContent($filename, $content);
    }

    function read($filename){return $this->_ioFile->read($filename);}

    function open($args=array()) {return $this->_ioFile->open($args);}
}
