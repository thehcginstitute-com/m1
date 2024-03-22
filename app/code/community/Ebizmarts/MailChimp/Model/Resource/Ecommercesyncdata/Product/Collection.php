<?php

/**
 * mc-magento Magento Component
 *
 * @category  Ebizmarts
 * @package   mc-magento
 * @author    Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date:     2019-11-04 17:32
 */
class Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Product_Collection extends
    Ebizmarts_MailChimp_Model_Resource_Ecommercesyncdata_Collection
{

    /**
     * Set resource type
     *
     * @return void
     */
    function _construct()
    {
        parent::_construct();
    }

    /**
     * @param Mage_Catalog_Model_Resource_Product_Collection $preFilteredProductsCollection
     */
    function joinLeftEcommerceSyncData($preFilteredProductsCollection)
    {
        $mailchimpTableName = $this->getMailchimpEcommerceDataTableName();
        $preFilteredProductsCollection->getSelect()->joinLeft(
            array('m4m' => $mailchimpTableName),
            "m4m.related_id = e.entity_id AND m4m.type = '" . Ebizmarts_MailChimp_Model_Config::IS_PRODUCT
            . "' AND m4m.mailchimp_store_id = '" . $this->getMailchimpStoreId() . "'",
            array('m4m.*')
        );
    }

    /**
     * @param $collection Mage_Catalog_Model_Resource_Product_Collection
     * @param $joinCondition
     */
    function executeMailchimpDataJoin($collection, $joinCondition)
    {
        $mailchimpTableName = $this->getMailchimpEcommerceDataTableName();
        $collection->getSelect()->joinLeft(
            array("m4m" => $mailchimpTableName),
            sprintf($joinCondition, Ebizmarts_MailChimp_Model_Config::IS_PRODUCT, $this->getMailchimpStoreId()),
            array(
                "m4m.related_id",
                "m4m.type",
                "m4m.mailchimp_store_id",
                "m4m.mailchimp_sync_delta",
                "m4m.mailchimp_sync_modified",
                "m4m.mailchimp_synced_flag"
            )
        );
    }

    /**
     * @param $deletedProducts
     */
    function joinMailchimpSyncDataDeleted($deletedProducts, $limit = null)
    {
        $this->joinLeftEcommerceSyncData($deletedProducts);
        $deletedProducts->getSelect()->where("m4m.mailchimp_sync_deleted = 1 AND m4m.mailchimp_sync_error = ''");

        if (isset($limit)) {
            $deletedProducts->getSelect()->limit($limit);
        }
    }

    function addJoinLeft($collection, array $array, $colString)
    {
        $collection->getSelect()->joinLeft($array, $colString);
    }

    function resetColumns($collection, $reset, $columns)
    {
        $collection->getSelect()->reset($reset)->columns($columns);
    }

    /**
     * @param $collection Mage_Catalog_Model_Resource_Product_Collection
     */
    function joinQtyAndBackorders($collection)
    {
        $collection->joinField(
            'qty',
            'cataloginventory/stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $collection->joinField(
            'backorders',
            'cataloginventory/stock_item',
            'backorders',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
    }

}
