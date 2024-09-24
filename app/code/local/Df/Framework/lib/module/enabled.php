<?php
use Varien_Simplexml_Element as MX;
/**
 * @used-by df_caller_entry_m()
 * @used-by df_log_l()
 */
function df_module_enabled(string $m):bool {return dfcf(function($m) {
	$moduleConfig = Mage::app()->getConfig()->getModuleConfig($m); /** @var MX|null $moduleConfig */
	return $moduleConfig && df_leaf_b($moduleConfig->{'active'});
}, [$m]);}
