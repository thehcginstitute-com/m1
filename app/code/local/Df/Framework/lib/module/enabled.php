<?php
/**
 * @used-by df_caller_entry_m()
 * @used-by df_log_l()
 */
function df_module_enabled(string $m):bool {return dfcf(function(string $m):bool {return df_bool(strval(
	Mage::app()->getConfig()->getModuleConfig($m)->is('active')
));}, [$m]);}