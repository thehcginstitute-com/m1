<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category    Mage
 * @package     Mage
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

# 2023-07-08 Dmitrii Fediuk https://upwork.com/fl/mage2pro
# 2023-07-10 # `^` instead of `&~` is incorrect:
# https://github.com/magento-russia/3/blob/2023-07-10/app/code/local/Df/Core/Boot.php#L146-L154
# 2024-01-11 "Port the modifications of `index.php` to Magento 1.9.4.5": https://github.com/thehcginstitute-com/m1/issues/117
error_reporting((E_ALL | E_STRICT) &~ (E_DEPRECATED));
ini_set('display_errors', 1);

define('MAGENTO_ROOT', getcwd());

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
$maintenanceFile = 'maintenance.flag';
$maintenanceIpFile = 'maintenance.ip';

require MAGENTO_ROOT . '/app/bootstrap.php';
require_once $mageFilename;

#Varien_Profiler::enable();

umask(0);

/* Store or website code */
$mageRunCode = $_SERVER['MAGE_RUN_CODE'] ?? '';

/* Run store or run website */
$mageRunType = $_SERVER['MAGE_RUN_TYPE'] ?? 'store';

if (file_exists($maintenanceFile)) {
    $maintenanceBypass = false;

    if (file_exists($maintenanceIpFile)) {
        /* if maintenanceFile and maintenanceIpFile are set use Mage to get remote IP (in order to respect remote_addr_headers xml config) */
        Mage::init($mageRunCode, $mageRunType);
        $currentIp = Mage::helper('core/http')->getRemoteAddr();
        $allowedIps = explode(',', trim(file_get_contents($maintenanceIpFile)));

        if (in_array($currentIp, $allowedIps)) {
            /* IP address matches, bypass maintenanceMode */
            $maintenanceBypass = true;
        }
    }
    if (!$maintenanceBypass) {
        include_once __DIR__ . '/errors/503.php';
        exit;
    }
}

Mage::run($mageRunCode, $mageRunType);
