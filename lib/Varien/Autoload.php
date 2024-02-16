<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Autoload
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Classes source autoload
 */
class Varien_Autoload
{
    /**
     * @var Varien_Autoload
     */
    protected static $_instance;

    /**
     * Singleton pattern implementation
     *
     * @return Varien_Autoload
     */
    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new Varien_Autoload();
        }
        return self::$_instance;
    }

    /**
     * Register SPL autoload function
     */
    public static function register()
    {
        spl_autoload_register([self::instance(), 'autoload']);
    }

    /**
     * Load class source code
     *
     * @param string $class
     */
    public function autoload($class)
    {
		# 2024-02-04 Dmitrii Fediuk https://upwork.com/fl/mage2pro
		# "Delete the unused `Mage_Compiler` module": https://github.com/thehcginstitute-com/m1/issues/363
		$path = str_replace(' ', DIRECTORY_SEPARATOR, ucwords(str_replace('_', ' ',
			# 2024-01-09 Dmitrii Fediuk https://upwork.com/fl/mage2pro
			# 1) «Support PHP namespaces»: https://github.com/thehcginstitute-com/m1/issues/139
			# 2) I ported it from
			# https://github.com/trackspecmotorsports/site/blob/2023-07-10/lib/Varien/Autoload.php#L89-L94
			# 3) I also used the same solution in in https://github.com/itsapiece
			str_replace('\\', '/', $class)
		))) . '.php';
        /** @see https://stackoverflow.com/a/5504486/716029 */
        $found = stream_resolve_include_path($path);
        if ($found !== false) {
            return include $found;
        }
    }
}
