<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Loginredirectpro
 * @author     Raj KB <magepsycho@gmail.com>
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Loginredirectpro_Model_System_Config_Source_Delays
{
    public function toOptionArray()
    {
        $options = array();
		for($i = 1; $i <= 5; $i++){
			$second = ($i == 1) ? Mage::helper('magepsycho_loginredirectpro')->__('second') : Mage::helper('magepsycho_loginredirectpro')->__('seconds');
			$options[$i] = $i . ' ' . $second;
		}
		return $options;
    }
}