<?php

/**
 * Cron Process available count limits options source
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MailChimp
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_ImageSize
{
    const BASE = 0;
    const SMALL = 1;
    const THUMBNAIL = 2;
    const ORIGINAL = 3;

    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return array(
            array('value' => self::BASE, 'label' => 'Base'),
            array('value' => self::SMALL, 'label' => 'Small'),
            array('value' => self::THUMBNAIL, 'label' => 'Thumbnail'),
            array('value' => self::ORIGINAL, 'label' => 'Original')
        );
    }
}
