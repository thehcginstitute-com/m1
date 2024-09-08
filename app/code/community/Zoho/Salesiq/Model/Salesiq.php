<?php
/**
 * @category   Zoho
 * @package    Zoho_Salesiq
 * @author     SalesIQ Team
 * @website    http://www.zoho.com/salesiq
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Zoho_Salesiq_Model_Salesiq extends Mage_Core_Model_Abstract {

    /**
     * Options getter
     *
     * @return array
     */
    function toOptionArray()
    {
        return array(
            array('value' => 'all_pages', 'label'=>Mage::helper('salesiq')->__('All Pages')),
            array('value' => 'cms_index_index', 'label'=>Mage::helper('salesiq')->__('Home Page')),
            array('value' => 'catalog_category_view', 'label'=>Mage::helper('salesiq')->__('Category Pages')),
            array('value' => 'catalog_product_view', 'label'=>Mage::helper('salesiq')->__('Product Pages')),
            array('value' => 'checkout_cart_index', 'label'=>Mage::helper('salesiq')->__('My Cart')),
            array('value' => 'checkout_onepage_index', 'label'=>Mage::helper('salesiq')->__('Checkout'))
        );
    }

}
