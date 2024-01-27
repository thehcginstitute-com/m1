<?php

/**
 * @category   MagePsycho
 * @package    MagePsycho_Storerestrictionpro
 * @author     magepsycho@gmail.com
 * @website    http://www.magepsycho.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class MagePsycho_Storerestrictionpro_Model_System_Config_Source_Paymentmethods
{
    protected $_options;

    function getAllOptions($withEmpty = false)
    {
        if (is_null($this->_options)) {
            $payments = Mage::getSingleton('payment/config')->getActiveMethods();
            $this->_options = array();
            foreach ($payments as $paymentCode => $paymentModel) {
                $paymentTitle = Mage::getStoreConfig('payment/' . $paymentCode . '/title');
                $this->_options[] = array('value' => $paymentCode, 'label' => $paymentTitle);
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