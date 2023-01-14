<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Paygate
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Payjunction Payment Action Dropdown source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payjunction_Model_Payjunction_Source_PaymentAction
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Payjunction_Model_Payjunction::ACTION_AUTHORIZE,
                'label' => Mage::helper('paygate')->__('Authorize Only')
            ),
            array(
                'value' => Mage_Payjunction_Model_Payjunction::ACTION_AUTHORIZE_CAPTURE,
                'label' => Mage::helper('paygate')->__('Authorize and Capture')
            ),
        );
    }
}
