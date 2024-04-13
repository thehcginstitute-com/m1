<?php
/**
 * 2024-04-14
 * @used-by hcg_mc_cfg_save()
 * @used-by hcg_mc_cfg_save_a()
 */
function df_cache_clean_cfg():void {Mage::getConfig()->cleanCache();}