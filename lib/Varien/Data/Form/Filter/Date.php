<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Data
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Form Input/Output Strip HTML tags Filter
 *
 * @category   Varien
 * @package    Varien_Data
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Varien_Data_Form_Filter_Date implements Varien_Data_Form_Filter_Interface
{
    /**
     * Date format
     *
     * @var string
     */
    protected $_dateFormat;

    /**
     * Local
     *
     * @var Zend_Locale
     */
    protected $_locale;

    /**
     * Initialize filter
     *
     * @param string $format    Zend_Date input/output format
     * @param Zend_Locale $locale
     */
    public function __construct($format = null, $locale = null)
    {
        if (is_null($format)) {
            $format = Varien_Date::DATE_INTERNAL_FORMAT;
        }
        $this->_dateFormat  = $format;
        $this->_locale      = $locale;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function inputFilter($value) {
		# 2024-03-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "`Varien_Data_Form_Filter_Date::inputFilter()` should throw an exception with a user-friendly message on a PHP error":
		# https://github.com/thehcginstitute-com/m1/issues/485
		try {
			$filterInput = new Zend_Filter_LocalizedToNormalized([
				'date_format'   => $this->_dateFormat,
				'locale'        => $this->_locale
			]);
			$filterInternal = new Zend_Filter_NormalizedToLocalized([
				'date_format'   => Varien_Date::DATE_INTERNAL_FORMAT,
				'locale'        => $this->_locale
			]);
			$value = $filterInput->filter($value);
			$value = $filterInternal->filter($value);
		}
		catch (Exception $e) {
			/**
			 * 2024-03-16 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			 * 1) In most cases, it will be caught by @see \Mage_Eav_Model_Attribute_Data_Abstract::_applyInputFilter()
			 * 2) "`Mage_Eav_Model_Attribute_Data_Abstract::_applyInputFilter()` should throw an exception
			 * with a user-friendly message on a PHP error": https://github.com/thehcginstitute-com/m1/issues/486
			 */
			throw new Exception("The value «{$value}» seems to be invalid as a date.", $e);
		}
        return $value;
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     */
    public function outputFilter($value)
    {
        $filterInput = new Zend_Filter_LocalizedToNormalized([
            'date_format'   => Varien_Date::DATE_INTERNAL_FORMAT,
            'locale'        => $this->_locale
        ]);
        $filterInternal = new Zend_Filter_NormalizedToLocalized([
            'date_format'   => $this->_dateFormat,
            'locale'        => $this->_locale
        ]);

        $value = $filterInput->filter($value);
        $value = $filterInternal->filter($value);
        return $value;
    }
}
