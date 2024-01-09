<?php
/**
 * @used-by df_log_l()
 * @param string $m
 * @return bool
 */
function df_module_enabled($m) {return dfcf(function($m) {
	/** @var Varien_Simplexml_Element|null $moduleConfig */
	$moduleConfig = Mage::app()->getConfig()->getModuleConfig($m);
	return $moduleConfig && df_leaf_b($moduleConfig->{'active'});
}, [$m]);}
