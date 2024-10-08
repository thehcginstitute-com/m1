<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Media
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Media library image config interface
 *
 * @category   Mage
 * @package    Mage_Media
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Media_Model_Image_Config_Interface
{
    /**
     * Retrieve base url for media files
     *
     * @return string
     */
    function getBaseMediaUrl();

    /**
     * Retrieve base path for media files
     *
     * @return string
     */
    function getBaseMediaPath();

    /**
     * Retrieve url for media file
     *
     * @param string $file
     * @return string
     */
    function getMediaUrl($file);

    /**
     * Retrieve file system path for media file
     *
     * @param string $file
     * @return string
     */
    function getMediaPath($file);
}
