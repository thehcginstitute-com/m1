<?php
/**
 * mailchimp-lib Magento Component
 *
 * @category  Ebizmarts
 * @package   mailchimp-lib
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     5/2/16 3:59 PM
 * @file:     Ecommerce.php
 */
class MailChimp_Ecommerce extends MailChimp_Abstract
{
    /**
     * @var MailChimp_EcommerceStore
     */
    public $stores;
    /**
     * @var MailChimp_EcommerceCarts
     */
    public $carts;
    /**
     * @var MailChimp_EcommerceCustomers
     */
    public $customers;
    /**
     * @var MailChimp_EcommerceOrders
     */
    public $orders;
    /**
     * @var MailChimp_EcommerceProducts
     */
    public $products;
    /**
     * @var MailChimp_EcommercePromoRules
     */
    public $promoRules;

    /**
     * @return MailChimp_EcommerceStore
     */
    function getStores()
    {
        return $this->stores;
    }

    /**
     * @return MailChimp_EcommerceCarts
     */
    function getCarts()
    {
        return $this->carts;
    }

    /**
     * @return MailChimp_EcommerceCustomers
     */
    function getCustomers()
    {
        return $this->customers;
    }

    /**
     * @return MailChimp_EcommerceOrders
     */
    function getOrders()
    {
        return $this->orders;
    }

    /**
     * @return MailChimp_EcommerceProducts
     */
    function getProducts()
    {
        return $this->products;
    }

    /**
     * @return MailChimp_EcommercePromoRules
     */
    function getPromoRules()
    {
        return $this->promoRules;
    }
}
