<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/** @var Mage_Core_Model_Resource_Setup $installer */
$installer = $this;
$installer->startSetup();

# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused `Mage_Paypal` module": https://github.com/thehcginstitute-com/m1/issues/356
# 2024-02-05 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# "Delete the unused `Mage_Usa` module": https://github.com/thehcginstitute-com/m1/issues/374
$rows = $installer->getConnection()->fetchAll(
    "select * from {$this->getTable('core_config_data')} where
    path in (
	'payment/authorizenet/login', 'payment/authorizenet/trans_key',
    'payment/verisign/pwd', 'payment/verisign/user')"
);

$hlp = Mage::helper('core');
foreach ($rows as $r) {
    if (!empty($r['value'])) {
        $r['value'] = $hlp->encrypt($r['value']);
        $installer->getConnection()->update($this->getTable('core_config_data'), $r, 'config_id=' . $r['config_id']);
    }
}
$installer->endSetup();
