<?php

/**
 * Cron Process available count limits options source
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MailChimp
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_OrderGrid
{
    const NONE = 0;
    const ICON = 1;
    const SYNCED = 2;
    const BOTH = 3;


    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return array(
            array('value' => self::NONE, 'label' => 'None'),
            array('value' => self::ICON, 'label' => 'Icon for Mailchimp orders'),
            array('value' => self::SYNCED, 'label' => 'If orders are synced to Mailchimp'),
            array('value' => self::BOTH, 'label' => 'Both')
        );
    }
}
