<?php
use Df_Core_Model_Layout as L;

/**
 * @used-by df_block()
 * @used-by df_block_l()
 * @used-by df_render_l()
 */
function df_layout():L {return Mage::getSingleton('core/layout');}