<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_System_Config_Source_Restrictiontypes
{

    const RESTRICTION_TYPE_NON_RESTRICTED           = 1;
    const RESTRICTION_TYPE_RESTRICTED_ACCESSIBLE    = 2;
    const RESTRICTION_TYPE_ACCESSIBLE_RESTRICTED    = 3;

    protected $_options;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'value' => self::RESTRICTION_TYPE_NON_RESTRICTED,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Non Restricted'),
                ),

                array(
                    'value' => self::RESTRICTION_TYPE_RESTRICTED_ACCESSIBLE,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Restricted (Only Configured Pages Accessible)'),
                ),

                array(
                    'value' => self::RESTRICTION_TYPE_ACCESSIBLE_RESTRICTED,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Accessible (Only Configured Pages/Sections Restricted)'),
                ),
            );

        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, array('value' => '', 'label' => ''));
        }
        return $options;
    }

    public function getOptionsArray($withEmpty = true)
    {
        $options = array();
        foreach ($this->getAllOptions($withEmpty) as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    public function getOptionText($value)
    {
        $options = $this->getAllOptions(false);
        foreach ($options as $item) {
            if ($item['value'] == $value) {
                return $item['label'];
            }
        }
        return false;
    }

    public function toOptionArray()
    {
        return $this->getAllOptions();
    }
}