<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Export entity product type simple model
 *
 * @category   Mage
 * @package    Mage_ImportExport
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_ImportExport_Model_Export_Entity_Product_Type_Simple extends Mage_ImportExport_Model_Export_Entity_Product_Type_Abstract
{
    /**
     * Overriden attributes parameters.
     *
     * @var array
     */
    protected $_attributeOverrides = [
        'has_options'      => ['source_model' => 'eav/entity_attribute_source_boolean'],
        'required_options' => ['source_model' => 'eav/entity_attribute_source_boolean'],
        'created_at'       => ['backend_type' => 'datetime'],
        'updated_at'       => ['backend_type' => 'datetime']
    ];

    /**
     * Array of attributes codes which are disabled for export.
     *
     * @var array
     */
    protected $_disabledAttrs = [
        'old_id',
		# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
        'tier_price',
        'group_price',
        'category_ids'
    ];
}
