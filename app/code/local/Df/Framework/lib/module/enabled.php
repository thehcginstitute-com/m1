<?php
use Varien_Simplexml_Element as MX;
/**
 * @used-by df_caller_entry_m()
 * @used-by df_log_l()
 */
function df_module_enabled(string $m):bool {return dfcf(function(string $m):bool {
	$x = Mage::app()->getConfig()->getModuleConfig($m); /** @var MX $x */
	return $x && df_leaf_b($x->{'active'});
}, [$m]);}
