<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth token resource collection model
 *
 * @category   Mage
 * @package    Mage_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_Model_Resource_Token_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('oauth/token');
    }

    /**
     * Load collection with consumer data
     *
     * Method use for show applications list (token-consumer)
     *
     * @return $this
     */
    function joinConsumerAsApplication()
    {
        $select = $this->getSelect();
        $select->joinLeft(
            ['c' => $this->getTable('oauth/consumer')],
            'c.entity_id = main_table.consumer_id',
            'name'
        );

        return $this;
    }

    /**
     * Add filter by admin ID
     *
     * @param int $adminId
     * @return $this
     */
    function addFilterByAdminId($adminId)
    {
        $this->addFilter('main_table.admin_id', $adminId);
        return $this;
    }

    /**
     * Add filter by customer ID
     *
     * @param int $customerId
     * @return $this
     */
    function addFilterByCustomerId($customerId)
    {
        $this->addFilter('main_table.customer_id', $customerId);
        return $this;
    }

    /**
     * Add filter by consumer ID
     *
     * @param int $consumerId
     * @return $this
     */
    function addFilterByConsumerId($consumerId)
    {
        $this->addFilter('main_table.consumer_id', $consumerId);
        return $this;
    }

    /**
     * Add filter by type
     *
     * @param string $type
     * @return $this
     */
    function addFilterByType($type)
    {
        $this->addFilter('main_table.type', $type);
        return $this;
    }

    /**
     * Add filter by ID
     *
     * @param array|int $id
     * @return $this
     */
    function addFilterById($id)
    {
        $this->addFilter('main_table.entity_id', ['in' => $id], 'public');
        return $this;
    }

    /**
     * Add filter by "Is Revoked" status
     *
     * @param bool|int $flag
     * @return $this
     */
    function addFilterByRevoked($flag)
    {
        $this->addFilter('main_table.revoked', (int) $flag, 'public');
        return $this;
    }
}
