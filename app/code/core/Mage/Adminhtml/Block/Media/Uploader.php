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
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml media library uploader
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated
 */
class Mage_Adminhtml_Block_Media_Uploader extends Mage_Uploader_Block_Multiple
{
    function __construct()
    {
        parent::__construct();
        $this->getUploaderConfig()->setTarget(Mage::getModel('adminhtml/url')->addSessionParam()->getUrl('*/*/upload'));
        $this->getUploaderConfig()->setFileParameterName('file');
    }
}
