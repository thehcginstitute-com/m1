<?php
use Varien_Io_File as F;

/**
 * 2024-04-20 "Port `df_fs_delete()` from `mage2pro/core`": https://github.com/thehcginstitute-com/m1/issues/570
 * @used-by hcg_mc_batch_delete() (https://github.com/thehcginstitute-com/m1/issues/569)
 */
function df_fs_delete(string $p):void {F::rmdirRecursive(df_param_sne($p, 0));}