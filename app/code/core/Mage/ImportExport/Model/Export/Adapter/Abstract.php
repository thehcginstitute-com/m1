<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract adapter model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @property resource $_fileHandler
 * @property string $_delimiter
 * @property string $_enclosure
 */
abstract class Mage_ImportExport_Model_Export_Adapter_Abstract
{
    /**
     * Destination file path.
     *
     * @var string
     */
    protected $_destination;

    /**
     * Header columns names.
     *
     * @var array
     */
    protected $_headerCols = null;

    /**
     * Count of rows
     *
     * @var int
     */
    protected $_rowsCount = 0;

    /**
     * Adapter object constructor.
     *
     * @param string $destination OPTIONAL Destination file path.
     * @throws Exception
     */
    final function __construct($destination = null)
    {
        register_shutdown_function([$this, 'destruct']);

        if (!$destination) {
            $destination = tempnam(sys_get_temp_dir(), 'importexport_');
        }
        if (!is_string($destination)) {
            Mage::throwException(Mage::helper('importexport')->__('Destination file path must be a string'));
        }
        $pathinfo = pathinfo($destination);

        if (empty($pathinfo['dirname']) || !is_writable($pathinfo['dirname'])) {
            Mage::throwException(Mage::helper('importexport')->__('Destination directory is not writable'));
        }
        if (is_file($destination) && !is_writable($destination)) {
            Mage::throwException(Mage::helper('importexport')->__('Destination file is not writable'));
        }
        $this->_destination = $destination;

        $this->_init();
    }

    /**
     * Destruct method on shutdown
     */
    function destruct()
    {
    }

    /**
     * Method called as last step of object instance creation. Can be overridden in child classes.
     *
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    protected function _init()
    {
        return $this;
    }

    /**
     * Get contents of export file.
     *
     * @return string
     */
    function getContents()
    {
        return file_get_contents($this->_destination);
    }

    /**
     * MIME-type for 'Content-Type' header.
     *
     * @return string
     */
    function getContentType()
    {
        return 'application/octet-stream';
    }

    /**
     * Return file extension for downloading.
     *
     * @return string
     */
    function getFileExtension()
    {
        return '';
    }

    /**
     * Get count of wrote lines
     *
     * @return int
     */
    function getRowsCount()
    {
        return $this->_rowsCount;
    }

    /**
     * Set column names.
     *
     * @param array $headerCols
     * @throws Exception
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    function setHeaderCols(array $headerCols)
    {
        if ($this->_headerCols !== null) {
            Mage::throwException(Mage::helper('importexport')->__('Header column names already set'));
        }
        if ($headerCols) {
            foreach ($headerCols as $colName) {
                $this->_headerCols[$colName] = false;
            }
            fputcsv($this->_fileHandler, array_keys($this->_headerCols), $this->_delimiter, $this->_enclosure);
        }
        return $this;
    }

    /**
     * Returns destination path
     * @return string
     */
    function getDestination()
    {
        return $this->_destination;
    }

    /**
     * Write row data to source file.
     *
     * @param array $rowData
     * @return Mage_ImportExport_Model_Export_Adapter_Abstract
     */
    abstract function writeRow(array $rowData);
}
