<?php
/**
 * Cron Process available count limits options source
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MageMonkey
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_ConnectionTimeout
{
    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return array(
            array('value' => 10, 'label' => 10),
            array('value' => 20, 'label' => 20),
            array('value' => 30, 'label' => 30),
            array('value' => 40, 'label' => 40),
            array('value' => 50, 'label' => 50),
            array('value' => 60, 'label' => 60)
        );
    }
}
