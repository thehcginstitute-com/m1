<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_System_Config_Source_Shippingmethods
{
    protected $_options;

    function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {

            $methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
            $this->_options = array();
            foreach ($methods as $shippingCode => $shippingModel) {
                $shippingTitle = Mage::getStoreConfig('carriers/' . $shippingCode . '/title');
                if (strlen($shippingTitle)) {
                    $this->_options[] = array('value' => $shippingCode, 'label' => $shippingTitle);
                }
            }
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