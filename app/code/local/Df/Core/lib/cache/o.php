<?php
use Df\Core\RAM;
/**
 * 2017-08-28
 * @used-by df_cache_clean()
 * @used-by df_cache_clean_tag()
 * @used-by dfcf()
 * @return RAM
 */
function df_ram() {return RAM::s();}