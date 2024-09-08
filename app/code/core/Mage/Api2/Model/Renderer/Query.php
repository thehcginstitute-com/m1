<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Api2
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Webservice API2 renderer of query format model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Renderer_Query implements Mage_Api2_Model_Renderer_Interface
{
    /**
     * Adapter mime type
     */
    public const MIME_TYPE = 'text/plain';

    /**
     * Convert Array to URL-encoded query string
     *
     * @param array|object $data
     * @return string
     */
    function render($data)
    {
        return http_build_query($data);
    }

    /**
     * Get MIME type generated by renderer
     *
     * @return string
     */
    function getMimeType()
    {
        return self::MIME_TYPE;
    }
}
