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
 * Payjunction Payment CC Types Source Model
 *
 * @category    Mage
 * @package     Mage_Paygate
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payjunction_Model_Payjunction_Source_AvsType
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '',
                'label' => 'None'
            ),		
            array(
                'value' => 'AWZ',
                'label' => 'Match Address OR Zip'
            ),
            array(
                'value' => 'XY',
                'label' => 'Match Address AND Zip'
            ),
            array(
                'value' => 'AW',
                'label' => 'Match Address OR 9 Digit Zip'
            ),
            array(
                'value' => 'AZ',
                'label' => 'Match Address OR 5 Digit Zip'
            ),
            array(
                'value' => 'A',
                'label' => 'Match Address'
            ),
            array(
                'value' => 'X',
                'label' => 'Match Address AND 9 Digit Zip'
            ),
            array(
                'value' => 'Y',
                'label' => 'Match Address AND 5 Digit Zip'
            ),
            array(
                'value' => 'W',
                'label' => 'Match 9 Digit Zip'
            ),
            array(
                'value' => 'Z',
                'label' => 'Match 5 Digit Zip'
            ),
        );
    }
}
