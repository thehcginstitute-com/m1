<?php
/**
 * Cron Process available count limits options source
 *
 * @category Ebizmarts
 * @package  Ebizmarts_MageMonkey
 * @author   Ebizmarts Team <info@ebizmarts.com>
 * @license  http://opensource.org/licenses/osl-3.0.php
 */
class Ebizmarts_MailChimp_Model_System_Config_Source_BatchLimit
{
    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return array(
            array('value' => 50, 'label' => 50),
            array('value' => 100, 'label' => 100),
            array('value' => 200, 'label' => 200),
            array('value' => 500, 'label' => 500)
        );
    }
}
