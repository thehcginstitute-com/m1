<?php
use Mage_Core_Model_Design as D;
/**
 * 2016-11-29
 * 2024-05-11 "Port `df_design()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/598
 */
function df_design():D {return Mage::getSingleton('core/design');}