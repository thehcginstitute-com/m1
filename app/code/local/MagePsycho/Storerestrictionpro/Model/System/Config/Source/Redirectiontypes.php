<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_System_Config_Source_Redirectiontypes
{
    const REDIRECTION_TYPE_LOGIN    = 1;
    const REDIRECTION_TYPE_CMS      = 2;
    const REDIRECTION_TYPE_CUSTOM   = 3;

    protected $_options;

    function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'value' => self::REDIRECTION_TYPE_LOGIN,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Login Page'),
                ),
                array(
                    'value' => self::REDIRECTION_TYPE_CMS,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('CMS Page'),
                ),
                array(
                    'value' => self::REDIRECTION_TYPE_CUSTOM,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Custom Page'),
                ),
            );

        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array('value' => '', 'label' => ''));
        }
        return $options;
    }

    function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    function toOptionArray()
    {
        return $this->getAllOptions();
    }
}