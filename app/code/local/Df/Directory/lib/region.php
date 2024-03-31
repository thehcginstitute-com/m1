<?php
use Mage_Directory_Model_Region as R;
/**
 * 2019-06-13
 * 2024-03-31 "Port `df_region_name()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/535
 * @param array(string => string) $a
 */
function df_region_name(array $a):string {/** @var string $r */
	if (!($r = dfa($a, 'region'))) {
		$o = Mage::getModel('directory/region'); /** @var R $o */
		$o->load(dfa($a, 'region_id'));
		$r = $o->getName();
	}
	return df_ets($r);
}