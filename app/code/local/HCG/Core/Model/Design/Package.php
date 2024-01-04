<?php
use HCG_Core_StaticContent as SC;
# 2024-01-04
# "Port the modifications of `app/code/core/Mage/Core/Model/Design/Package.php` to Magento 1.9.4.5":
# https://github.com/thehcginstitute-com/m1/issues/96
class HCG_Core_Model_Design_Package extends Mage_Core_Model_Design_Package {
	/**
	 * 2024-01-04
	 * @override
	 * @see Mage_Core_Model_Design_Package::getSkinUrl()
	 * @param string|null $f [optional]
	 * @param array(string => mixed) $p [optional]
	 */
	function getSkinUrl($f = null, array $p = []):string {return parent::getSkinUrl($f, $p) . '?v=' . SC::V;}
}
