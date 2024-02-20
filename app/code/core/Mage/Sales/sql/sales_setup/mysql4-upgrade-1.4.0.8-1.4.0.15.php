<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Sales_Model_Entity_Setup $installer */
$installer = $this;

$orderGridTable             = $installer->getTable('sales/order_grid');
$orderTable                 = $installer->getTable('sales/order');
$paymentTransactionTable    = $installer->getTable('sales/payment_transaction');
# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
$orderItemTable             = $installer->getTable('sales_flat_order_item');
$flatOrderTable             = $installer->getTable('sales_flat_order');
# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401
$customerEntityTable        = $installer->getTable('customer_entity');
$coreStoreTable             = $installer->getTable('core_store');
# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused «Billing Agreements» feature": https://github.com/thehcginstitute-com/m1/issues/400

//-------
$installer->getConnection()->addColumn(
    $orderGridTable,
    'store_name',
    'varchar(255) null default null AFTER `store_id`'
);

$installer->run("
    UPDATE {$orderGridTable} AS og
        INNER JOIN  {$orderTable} AS o on (og.entity_id=o.entity_id)
    SET
        og.store_name = o.store_name
");

//-------
$installer->getConnection()->addColumn(
    $paymentTransactionTable,
    'created_at',
    'DATETIME NULL'
);

//-------
$this->getConnection()->addColumn($orderItemTable, 'is_nominal', 'int NOT NULL DEFAULT \'0\'');

# 2024-02-21 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# 1) "Delete the unused «Billing Agreements» feature": https://github.com/thehcginstitute-com/m1/issues/400
# 2) "Delete the unused «Recurring Profiles» feature": https://github.com/thehcginstitute-com/m1/issues/401