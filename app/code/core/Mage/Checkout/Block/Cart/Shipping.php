<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Checkout_Block_Cart_Shipping extends Mage_Checkout_Block_Cart_Abstract
{
    /**
     * Available Carriers Instances
     * @var null|array
     */
    protected $_carriers = null;

    /**
     * Estimate Rates
     * @var array
     */
    protected $_rates = [];

    /**
     * Address Model
     *
     * @var array
     */
    protected $_address = [];

    /**
     * Get Estimate Rates
     *
     * @return array
     */
    function getEstimateRates()
    {
        if (empty($this->_rates)) {
            $groups = $this->getAddress()->getGroupedAllShippingRates();
            $this->_rates = $groups;
        }
        return $this->_rates;
    }

    /**
     * Get Address Model
     *
     * @return Mage_Sales_Model_Quote_Address
     */
    function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Get Carrier Name
     *
     * @param string $carrierCode
     * @return mixed
     */
    function getCarrierName($carrierCode)
    {
        if ($name = Mage::getStoreConfig('carriers/' . $carrierCode . '/title')) {
            return $name;
        }
        return $carrierCode;
    }

    /**
     * Get Shipping Method
     *
     * @return string
     */
    function getAddressShippingMethod()
    {
        return $this->getAddress()->getShippingMethod();
    }

    /**
     * Get Estimate Country Id
     *
     * @return string
     */
    function getEstimateCountryId()
    {
        return $this->getAddress()->getCountryId();
    }

    /**
     * Get Estimate Postcode
     *
     * @return string
     */
    function getEstimatePostcode()
    {
        return $this->getAddress()->getPostcode();
    }

    /**
     * Get Estimate City
     *
     * @return string
     */
    function getEstimateCity()
    {
        return $this->getAddress()->getCity();
    }

    /**
     * Get Estimate Region Id
     *
     * @return mixed
     */
    function getEstimateRegionId()
    {
        return $this->getAddress()->getRegionId();
    }

    /**
     * Get Estimate Region
     *
     * @return string
     */
    function getEstimateRegion()
    {
        return $this->getAddress()->getRegion();
    }

	# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
	# "Delete the unused `Mage_Usa` module": https://github.com/thehcginstitute-com/m1/issues/374

    /**
     * Show State in Shipping Estimation
     *
     * @return bool
     */
    function getStateActive() {
		# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused `Mage_Usa` module": https://github.com/thehcginstitute-com/m1/issues/374
        return Mage::getStoreConfigFlag('carriers/tablerate/active');
    }

    /**
     * Convert price from default currency to current currency
     *
     * @param float $price
     * @return float
     */
    function formatPrice($price)
    {
        return $this->getQuote()->getStore()->convertPrice($price, true);
    }

    /**
     * Get Shipping Price
     *
     * @param float $price
     * @param bool $flag
     * @return float
     */
    function getShippingPrice($price, $flag)
    {
        /** @var Mage_Tax_Helper_Data $helper */
        $helper = $this->helper('tax');
        return $this->formatPrice($helper->getShippingPrice(
            $price,
            $flag,
            $this->getAddress(),
            $this->getQuote()->getCustomerTaxClassId()
        ));
    }

    /**
     * Obtain available carriers instances
     *
     * @return array
     */
    function getCarriers()
    {
        if ($this->_carriers === null) {
            $this->_carriers = [];
            $this->getEstimateRates();
            foreach ($this->_rates as $rateGroup) {
                if (!empty($rateGroup)) {
                    foreach ($rateGroup as $rate) {
                        $this->_carriers[] = $rate->getCarrierInstance();
                    }
                }
            }
        }
        return $this->_carriers;
    }

    /**
     * Check if one of carriers require state/province
     *
     * @return bool
     */
    function isStateProvinceRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isStateProvinceRequired()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if one of carriers require city
     *
     * @return bool
     */
    function isCityRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isCityRequired()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if one of carriers require zip code
     *
     * @return bool
     */
    function isZipCodeRequired()
    {
        foreach ($this->getCarriers() as $carrier) {
            if ($carrier->isZipCodeRequired($this->getEstimateCountryId())) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return 'Estimate Shipping and Tax' form action url
     *
     * @return string
     */
    function getFormActionUrl()
    {
        return $this->getUrl('checkout/cart/estimatePost', ['_secure' => $this->_isSecure()]);
    }

    /**
     * Return 'Update Estimate Shipping and Tax' form action url
     *
     * @return string
     */
    function getUpdateFormActionUrl()
    {
        return $this->getUrl('checkout/cart/estimateUpdatePost', ['_secure' => $this->_isSecure()]);
    }
}
