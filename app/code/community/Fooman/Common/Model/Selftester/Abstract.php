<?php
/**
 * Fooman Common
 *
 * @package   Fooman_Common
 * @author    Kristof Ringleff <kristof@fooman.co.nz>
 * @copyright Copyright (c) 2012 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Fooman_Common_Model_Selftester_Abstract extends Mage_Core_Model_Abstract
{

    /**
     * Helper to test for rewrites
     *
     * @param array $currentRow The rewrite info
     *
     * @throws Exception
     *
     * @return void
     */
    protected function _testRewriteRow(array $currentRow)
    {
        switch ($currentRow[0]) {
            case 'resource-model':
                $model = Mage::getResourceModel($currentRow[1]);
                if (get_class($model) != $currentRow[2]) {
                    throw new Exception(
                        'Trying to load class ' . $currentRow[2] . 'returns ' . get_class($model)
                        . '. Please refresh your Magento configuration cache and check
                    if you have any conflicting extensions installed.'
                    );
                }
                break;

            case 'model':
                $model = Mage::getModel($currentRow[1]);
                if (!($model instanceof $currentRow[2])) {
                    throw new Exception(
                        'Trying to load class ' . $currentRow[2] . ' returns ' . get_class($model)
                        . '. Please refresh your Magento configuration cache and check
                    if you have any conflicting extensions installed.'
                    );
                }
                if (get_class($model) != $currentRow[2]) {
                    throw new Exception(
                        'Trying to load class ' . $currentRow[2] . ' returns correct instance but unexpected class '
                        . get_class($model). '. Please refresh your Magento configuration cache and check
                    if you have any conflicting extensions installed.'
                    );
                }
                break;
            case 'block':
                $block = Mage::app()->getLayout()->createBlock($currentRow[1]);
                if (!($block instanceof $currentRow[2])) {
                    throw new Exception(
                        'Trying to load block ' . $currentRow[2] . ' returns ' . get_class($block)
                        . '. Please refresh your Magento configuration cache and check
                    if you have any conflicting extensions installed.'
                    );
                }
                if (get_class($block) != $currentRow[2]) {
                    throw new Exception(
                        'Trying to load block ' . $currentRow[2] . ' returns correct instance but unexpected class '
                        . get_class($block). '. Please refresh your Magento configuration cache and check
                    if you have any conflicting extensions installed.'
                    );
                }
                break;
        }
    }

    /**
     * add Mage version to messages
     *
     * @return void
     */
    function _getVersions()
    {
        $this->messages[] = "Magento version: " . Mage::getVersion();
    }

    /**
     * stub for retrieval of database fields
     *
     * @return array
     */
    function _getDbFields()
    {
        return array();
    }

    /**
     * stub for retrieval of rewrite information
     *
     * @return array
     */
    function _getRewrites ()
    {
        return array();
    }

    /**
     * stub for list of files
     *
     * @return array
     */
    function _getFiles ()
    {
        return array();
    }

    /**
     * stub for db settings
     *
     * @return array
     */
    function _getSettings()
    {
        return array();
    }

}
