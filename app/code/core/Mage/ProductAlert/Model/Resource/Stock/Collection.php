<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product alert for back in stock collection
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ProductAlert_Model_Resource_Stock_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Define stock collection
     *
     */
    protected function _construct()
    {
        $this->_init('productalert/stock');
    }

    /**
     * Add customer filter
     *
     * @param mixed $customer
     * @return $this
     */
    function addCustomerFilter($customer)
    {
        $adapter = $this->getConnection();
        if (is_array($customer)) {
            $condition = $adapter->quoteInto('customer_id IN(?)', $customer);
        } elseif ($customer instanceof Mage_Customer_Model_Customer) {
            $condition = $adapter->quoteInto('customer_id=?', $customer->getId());
        } else {
            $condition = $adapter->quoteInto('customer_id=?', $customer);
        }
        $this->addFilter('customer_id', $condition, 'string');
        return $this;
    }

    /**
     * Add website filter
     *
     * @param mixed $website
     * @return $this
     */
    function addWebsiteFilter($website)
    {
        $adapter = $this->getConnection();
        if (is_null($website) || $website == 0) {
            return $this;
        }
        if (is_array($website)) {
            $condition = $adapter->quoteInto('website_id IN(?)', $website);
        } elseif ($website instanceof Mage_Core_Model_Website) {
            $condition = $adapter->quoteInto('website_id=?', $website->getId());
        } else {
            $condition = $adapter->quoteInto('website_id=?', $website);
        }
        $this->addFilter('website_id', $condition, 'string');
        return $this;
    }

    /**
     * Add status filter
     *
     * @param int $status
     * @return $this
     */
    function addStatusFilter($status)
    {
        $condition = $this->getConnection()->quoteInto('status=?', $status);
        $this->addFilter('status', $condition, 'string');
        return $this;
    }

    /**
     * Set order by customer
     *
     * @param string $sort
     * @return $this
     */
    function setCustomerOrder($sort = 'ASC')
    {
        $this->getSelect()->order('customer_id ' . $sort);
        return $this;
    }
}
