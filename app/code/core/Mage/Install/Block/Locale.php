<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Install localization block
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Install_Block_Locale extends Mage_Install_Block_Abstract
{
    function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/locale.phtml');
    }

    /**
     * Retrieve locale object
     *
     * @return Zend_Locale
     */
    function getLocale()
    {
        $locale = $this->getData('locale');
        if (is_null($locale)) {
            $locale = Mage::app()->getLocale()->getLocale();
            $this->setData('locale', $locale);
        }
        return $locale;
    }

    /**
     * Retrieve locale data post url
     *
     * @return string
     */
    function getPostUrl()
    {
        return $this->getCurrentStep()->getNextUrl();
        //return $this->getUrl('*/*/localePost');
    }

    /**
     * Retrieve locale change url
     *
     * @return string
     */
    function getChangeUrl()
    {
        return $this->getUrl('*/*/localeChange');
    }

    /**
     * Retrieve locale dropdown HTML
     *
     * @return string
     */
    function getLocaleSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[locale]')
            ->setId('locale')
            ->setTitle(Mage::helper('install')->__('Locale'))
            ->setClass('required-entry')
            ->setValue($this->getLocale()->__toString())
            ->setOptions(Mage::app()->getLocale()->getTranslatedOptionLocales())
            ->getHtml();
    }

    /**
     * Retrieve timezone dropdown HTML
     *
     * @return string
     */
    function getTimezoneSelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[timezone]')
            ->setId('timezone')
            ->setTitle(Mage::helper('install')->__('Time Zone'))
            ->setClass('required-entry')
            ->setValue($this->getTimezone())
            ->setOptions(Mage::app()->getLocale()->getOptionTimezones())
            ->getHtml();
    }

    /**
     * Retrieve timezone
     *
     * @return string
     */
    function getTimezone()
    {
        $timezone = Mage::getSingleton('install/session')->getTimezone()
            ? Mage::getSingleton('install/session')->getTimezone()
            : Mage::app()->getLocale()->getTimezone();
        if ($timezone == Mage_Core_Model_Locale::DEFAULT_TIMEZONE) {
            $timezone = 'America/Los_Angeles';
        }
        return $timezone;
    }

    /**
     * Retrieve currency dropdown html
     *
     * @return string
     */
    function getCurrencySelect()
    {
        return $this->getLayout()->createBlock('core/html_select')
            ->setName('config[currency]')
            ->setId('currency')
            ->setTitle(Mage::helper('install')->__('Default Currency'))
            ->setClass('required-entry')
            ->setValue($this->getCurrency())
            ->setOptions(Mage::app()->getLocale()->getOptionCurrencies())
            ->getHtml();
    }

    /**
     * Retrieve currency
     *
     * @return string
     */
    function getCurrency()
    {
        return Mage::getSingleton('install/session')->getCurrency()
            ? Mage::getSingleton('install/session')->getCurrency()
            : Mage::app()->getLocale()->getCurrency();
    }

    function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object();
            $this->setData('form_data', $data);
        }
        return $data;
    }
}
