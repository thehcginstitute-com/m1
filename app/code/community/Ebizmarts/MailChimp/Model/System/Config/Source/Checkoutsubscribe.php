<?php

/**
 * Checkout subscribe available status options source
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MailChimp
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_Checkoutsubscribe
{
    const DISABLED = 0;
    const CHECKED_BY_DEFAULT = 1;
    const NOT_CHECKED_BY_DEFAULT = 2;
    const FORCE_HIDDEN = 3;
    const FORCE_VISIBLE = 4;

    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return array(
            array('value' => self::CHECKED_BY_DEFAULT, 'label' => 'Enabled - Checked by default'),
            array('value' => self::NOT_CHECKED_BY_DEFAULT, 'label' => 'Enabled - Not Checked by default'),
            array('value' => self::FORCE_HIDDEN, 'label' => 'Enabled - Force subscription hidden'),
            array('value' => self::FORCE_VISIBLE, 'label' => 'Enabled - Force subscription'),
            array('value' => self::DISABLED, 'label' => '-- Disabled --')
        );
    }
}
