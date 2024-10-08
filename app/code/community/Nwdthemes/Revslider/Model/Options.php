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

class Nwdthemes_Revslider_Model_Options extends Mage_Core_Model_Abstract {

	/**
	 * Constructor
	 */

    function _construct() {
        parent::_construct();
        $this->_init('nwdrevslider/options');
    }

	/**
	 * Get option by key
	 *
	 * @param string $handle
	 * @param string $default
	 * @return string
	 */

	function getOption($handle, $default = '') {
		$_item = $this->getCollection()
			->addFieldToFilter('handle', $handle)
			->setPageSize(1)
			->getFirstItem();
		return $_item ? $_item->getData('option') : $default;
	}

	/**
	 * Update option
	 *
	 * @param string $handle
	 * @param string value
	 */

	function updateOption($handle, $option = '') {
		$_item = $this->getCollection()
			->addFieldToFilter('handle', $handle)
			->setPageSize(1)
			->getFirstItem();
		if ( ! $_item)
		{
			$_item = $this->getModel('nwdrevslider/options')->setData('handle', $handle);
		}
		$_item->setData('option', $option)->save();
	}

}
