<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml header block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Header extends Mage_Adminhtml_Block_Template
{
    function __construct()
    {
        parent::__construct();
        $this->setTemplate('page/header.phtml');
    }

    function getHomeLink()
    {
        return $this->getUrl('adminhtml');
    }

    function getUser()
    {
        return Mage::getSingleton('admin/session')->getUser();
    }

    function getLogoutLink()
    {
        return $this->getUrl('adminhtml/index/logout');
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return bool
     */
    function displayNoscriptNotice()
    {
        return Mage::getStoreConfig('web/browser_capabilities/javascript');
    }
}
