<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Customerregfields
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Customerregfields_Model_System_Config_Source_Domaintypes
{
    const DOMAIN_TYPE_PRODUCTION  = 1;
    const DOMAIN_TYPE_DEVELOPMENT = 2;

    protected $_options;

    function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'value' => self::DOMAIN_TYPE_PRODUCTION,
                    'label' => hcg_mp_hc()->__('Production'),
                ),

                array(
                    'value' => self::DOMAIN_TYPE_DEVELOPMENT,
                    'label' => hcg_mp_hc()->__('Development'),
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