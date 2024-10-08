<?php

/**
 * Nwdthemes Revolution Slider Extension
 *
 * @package     Revslider
 * @author		Nwdthemes <mail@nwdthemes.com>
 * @link		http://nwdthemes.com/
 * @copyright   Copyright (c) 2014. Nwdthemes
 * @license     http://themeforest.net/licenses/terms/regular
 */

class Nwdthemes_Revslider_Model_Observer
{
    function setHandle(Varien_Event_Observer $observer)
    {
		if ( Mage::helper('nwdall')->getCfg('general/enabled', 'nwdrevslider_config') )
		{
			$includeSlider = Mage::getModel('nwdrevslider/settings')->getSettingsValue('includes_globally') == 'on';

			if ( ! $includeSlider)
			{
				$fullActionName = $observer->getEvent()->getAction()->getFullActionName();
				$arrHandles = explode(',', Mage::getModel('nwdrevslider/settings')->getSettingsValue('pages_for_includes') );
				foreach ($arrHandles as $_handle) {
					if (trim($_handle) == $fullActionName)
					{
						$includeSlider = true;
					}
				}
			}

			if ($includeSlider)
			{
				Mage::app()->getLayout()->getUpdate()->addHandle('nwdrevslider_default');
			}
		}
    }
}
