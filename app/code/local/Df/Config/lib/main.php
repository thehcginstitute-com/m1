<?php
/**
 * 2024-05-10 "Port `df_cfg()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/592
 * @return mixed
 */
function df_cfg(string $k) {return Mage::getStoreConfig($k);}