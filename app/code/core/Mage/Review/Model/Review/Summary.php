<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review summary
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setStoreId(int $value)
 */
class Mage_Review_Model_Review_Summary extends Mage_Core_Model_Abstract
{
    function __construct()
    {
        $this->_init('review/review_summary');
    }

    /**
     * @return string
     */
    function getEntityPkValue()
    {
        return $this->_getData('entity_pk_value');
    }

    /**
     * @return array
     */
    function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    /**
     * @return int
     */
    function getReviewsCount()
    {
        return $this->_getData('reviews_count');
    }
}
