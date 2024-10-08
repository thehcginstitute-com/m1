<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth authorization base abstract block with auth buttons
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Oauth_Block_Authorize_ButtonBaseAbstract extends Mage_Oauth_Block_Authorize_Abstract
{
    /**
     * Get confirm url path
     *
     * @return string
     */
    abstract function getConfirmUrlPath();

    /**
     * Get reject url path
     *
     * @return string
     */
    abstract function getRejectUrlPath();

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    function getConfirmUrl()
    {
        return $this->getUrl($this->getConfirmUrlPath() . ($this->getIsSimple() ? 'Simple' : ''));
    }

    /**
     * Retrieve reject authorization url
     *
     * @return string
     */
    function getRejectUrl()
    {
        return $this->getUrl($this->getRejectUrlPath() . ($this->getIsSimple() ? 'Simple' : ''));
    }
}
