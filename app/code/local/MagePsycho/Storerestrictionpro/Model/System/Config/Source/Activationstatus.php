<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_System_Config_Source_Activationstatus
{
    const ACTIVATION_STATUS_ENABLED_WITH_MAIL        = 1;
    const ACTIVATION_STATUS_ENABLED_WITHOUT_MAIL     = 2;
    const ACTIVATION_STATUS_DISABLED_WITHOUT_MAIL    = 3;
    const ACTIVATION_STATUS_DISABLED_WITH_MAIL       = 4;

    protected $_options;

    public function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $this->_options = array(
                array(
                    'value' => self::ACTIVATION_STATUS_ENABLED_WITHOUT_MAIL,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Yes (Without Notification)'),
                ),
                array(
                    'value' => self::ACTIVATION_STATUS_ENABLED_WITH_MAIL,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('Yes (With Notification)'),
                ),
                array(
                    'value' => self::ACTIVATION_STATUS_DISABLED_WITHOUT_MAIL,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('No (Without Notification)'),
                ),
                array(
                    'value' => self::ACTIVATION_STATUS_DISABLED_WITH_MAIL,
                    'label' => Mage::helper('magepsycho_storerestrictionpro')->__('No (With Notification)'),
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